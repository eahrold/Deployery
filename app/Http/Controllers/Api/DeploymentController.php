<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BaseRequest as Request;
use App\Jobs\ServerDeploy;
use App\Models\Project;
use App\Models\Server;
use App\Services\Git\GitInfo;
use App\Services\Git\Validation\ValidRepoBranch;
use Dingo\Api\Routing\Helpers;

class DeploymentController extends Controller
{
    use Helpers;

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
     * @return JSON
     */
    public function deploy($project_id, $id)
    {
        $server = Project::findServer($project_id, $id);

        $this->apiValidate($this->request, [
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

        return $this->response->array(
            $this->queueDeployment($server, $to, $from, $this->user()->full_name, $options)
        );
    }

    /**
     * Validate request with api error response
     *
     * @param  Request $request request object
     * @param  array   $rules   validation rules
     * @throws UpdateResourceFailedException on validation failure
     * @return void
     */
    protected function apiValidate(Request $request, array $rules)
    {
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $class_name = class_basename($this->model);
            throw new \Dingo\Api\Exception\ResourceException(
                "he given data was invalid..",
                $validator->errors()
            );
        }
    }

    /**
     * Get details for the commit
     *
     * @param  integer $project_id Project ID
     * @param  integer $id         Server ID
     * @return \Dingo\Api\Http\Response|null
     */
    public function commitDetails($project_id, $id)
    {
        $cacheKey = "commit-details-{$project_id}-{$id}";
        if (request()->force === true) {
            \Cache::forget($cacheKey);
        }

        return \Cache::remember($cacheKey, 1, function() use ($project_id, $id) {
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
                return $this->response->array(
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

    public function getBranchCommits($project_id)
    {
        $project = Project::findOrFail($project_id);
        $this->authorize('deploy', $project);

        $branch = $this->request->get('branch');

        $gitInfo = (new GitInfo($project->repoPath()))->branch($branch);
        $commits = $gitInfo->commits(30);

        return $this->response->array($commits);
    }
    /**
     * Find a specific commit
     *
     * @param  integer $project_id Project ID
     * @param  integer $id         Server ID
     * @return \Dingo\Api\Http\Response|null
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

        return $this->response->array($commit);
    }

    /**
     * Trigger Deployment from frontend
     *
     * @return \Dingo\Api\Http\Response|null
     */
    public function webhook($webhook)
    {
        $info = (new \App\Services\WebHooks\WebhookHandler($this->request))->info();
        dump($info);

        $server = Server::where('webhook', $this->request->url())
                        ->where('branch', $info->branch)
                        ->firstOrFail();

        $sender = "{$info->user} [{$info->source}]";

        if (!$server->autodeploy) {
            return $this->response->error("Autodeploy is not enabled", 404);
        }

        $server->updateGitInfo();

        $from = $server->last_deployed_commit;
        $to = $server->newest_commit['hash'];

        $response = $this->queueDeployment($server, $to, $from, $sender);

        return $this->response->array($response);
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