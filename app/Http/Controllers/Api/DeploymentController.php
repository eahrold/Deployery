<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BaseRequest as Request;
use App\Jobs\ServerDeploy;
use App\Models\Project;
use App\Models\Server;
use App\Services\Git\GitInfo;
use App\Services\Git\Validation\ValidRepoBranch;

class DeploymentController extends Controller
{
    /**
     * @var \App\Http\Requests\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    //----------------------------------------------------------
    // Deployment  Endpoints
    //-------------------------------------------------------

    /**
     * Trigger Deployment from frontend
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deploy($project_id, $id)
    {
        $server = Project::findServer($project_id, $id);

        $data = $this->request->validate([
            'script_id' => 'sometimes|array',
            'script_ids.*' => 'exists:scripts,id',
            'use_branch_in_future' => 'sometimes|boolean',
            'branch' => [
                'sometimes', new ValidRepoBranch($server->repo)
            ]
        ]);

        $scriptIds = $this->request->get('script_ids', []);
        $oneOffScripts = $server->oneOffScripts()->whereIn('id', $scriptIds)->pluck('id')->values()->all();

        $this->authorize('deploy', $server->project);

        if ($this->request->get('deploy_entire_repo')) {
            $from = null;
            $to = $server->newest_commit['hash'];
        } else {
            $from = $this->request->get('from') ?: $server->last_deployed_commit;
            $to = $this->request->get('to') ?: $server->newest_commit['hash'];
        }

        $options = $this->request->only(['branch', 'use_branch_in_future']);
        $options['script_ids'] = $oneOffScripts;

        return response()->json(
            $this->queueDeployment($server, $to, $from, $this->user()->full_name, $options)
        );
    }


    /**
     * Get details for the commit
     *
     * @param  integer $project_id Project ID
     * @param  integer $id         Server ID
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function commitDetails($project_id, $id)
    {
        $cacheKey = "commit-details-{$project_id}-{$id}";
        if (request()->force === true) {
            \Cache::forget($cacheKey);
        }

        return \Cache::remember($cacheKey, 10, function() use ($project_id, $id) {
            $server = Project::findServer($project_id, $id);
            $this->authorize('deploy', $server->project);

            $server->updateGitInfo();

            $available_branches = $server->available_branches;
            $current_branch = $server->branch;

            $last_deployed_commit_details = $server->last_deployed_commit_details;
            $available_commits = $server->commits;

            $available_scripts = $server->oneOffScripts->map(function($script){
                return [
                    'id' => $script->id,
                    'description' => $script->description
                ];
            })->values();

            if ($server->validateConnection()) {
                return response()->json(
                    compact(
                        'last_deployed_commit_details',
                        'available_commits',
                        'available_scripts',
                        'available_branches',
                        'current_branch'
                    )
                );
            }
            abort(412, $server->present()->connection_status_message);
        });
    }

    /**
     * Get The Branch Commits
     *
     * @param  integer $project_id Project Id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBranchCommits($project_id)
    {
        $project = Project::findOrFail($project_id);
        $this->authorize('deploy', $project);

        $branch = $this->request->get('branch');

        $gitInfo = (new GitInfo($project->repoPath()))->branch($branch);
        $commits = $gitInfo->commits(30);

        return response()->json($commits);
    }
    /**
     * Find a specific commit
     *
     * @param  integer $project_id Project ID
     * @param  integer $id         Server ID
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function findCommit($project_id, $id)
    {
        $server = Project::findServer($project_id, $id);
        $this->authorize('deploy', $server->project);

        $hash = $this->request->get('commit');
        $commit = $server->findCommit($hash);

        if (!$commit) {
            abort(422, 'Could not find a matching commit');
        }

        return response()->json($commit);
    }

    /**
     * Trigger Deployment from webhook
     *
     * @param  string $webhook Webhook Param
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function webhook($webhook)
    {
        $info = (new \App\Services\WebHooks\WebhookHandler($this->request))->info();

        $server = Server::where('webhook', $this->request->url())
                        ->where('branch', $info->branch)
                        ->firstOrFail();

        $sender = "{$info->user} [{$info->source}]";

        abort_unless($server->autodeploy, 404, "Autodeploy is not enabled");

        $server->updateGitInfo();

        $from = $server->last_deployed_commit;
        $to = $server->newest_commit['hash'];

        $response = $this->queueDeployment($server, $to, $from, $sender);

        return response()->json($response);
    }

    //----------------------------------------------------------
    // Private
    //-------------------------------------------------------

    /**
     * Add the deployment to the quque
     *
     * @param  Server      $server    Server being deployed to
     * @param  string|null $to        Commit getting deployed to
     * @param  string|null $from      Commite getting deployed from
     * @param  string|null $user_name User deploying
     *
     * @return array  Message
     */
    private function queueDeployment(Server $server, string $to = null, string $from = null, $user_name = null, $options=[])
    {
        if ($user_name === '' || $user_name === null) {
            $user_name = $server->project->user->username;
        }

        $deployment = (new ServerDeploy($server, $user_name, $from, $to, $options))->onQueue('deployments');
        $this->dispatch($deployment);

        return [
            'message'=>'Queued deployment',
            'from' => $from ?: "Beginning of time",
            'to' => $to ?: "Autodetecting"
        ];
    }

}