<?php

namespace App\Jobs;

use App\Events\DeploymentEnded;
use App\Events\DeploymentProgress;
use App\Events\DeploymentStarted;
use App\Jobs\Job;
use App\Models\History;
use App\Models\Server;
use App\Notifications\DeploymentComplete;
use App\Services\DeploymentProcess;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ServerDeploy extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Server Object
     * @var \App\Models\Server
     */
    private $server;

    /**
     * Webcsocket channel identifier
     * @var string
     */
    private $channel;

    /**
     * From Commit
     * @var string
     */
    private $fromCommit;

    /**
     * To Commit
     * @var string
     */
    private $toCommit;

    /**
     * User Name
     * @var string
     */
    private $user_name;

    /**
     * Options
     * @var array
     */
    private $options;

    /**
     * When the deployment started
     * @var boolean
     */
    private $deployment_started;

    private $original_branch;

    /**
     * Create a new job instance.
     * @param null|string $from
     * @param null|string $to
     */
    public function __construct(Server $server, $user_name, $from, $to, $options = [])
    {
        $this->server = $server;
        $this->original_branch = $server->branch;

        $this->channel = $server->channel_id;

        $this->fromCommit = $from;
        $this->toCommit = $to;
        $this->user_name = $user_name;
        $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if ($this->postponeIfNeeded()) {
            return;
        }

        $this->registerDeploymentStarted(
            $this->server->present()->deployment_started_message
        );

        $this->prepareServer();

        $this->toCommit = $this->toCommit ?: $this->server->newest_commit['hash'];

        $process = (new DeploymentProcess($this->server))->setCallback(function ($message, $percent=0, $stage=null) {
            $this->sendMessage($message, [], $percent, $stage);
            $this->server->is_deploying = true;
        });

        $scripts = data_get($this->options, 'script_ids', []);

        $rc = $process->deploy($this->toCommit, $this->fromCommit, $scripts);

        $changes = $process->getChanges();
        $errors = $process->getErrors();

        $this->restoreServerIfNeeded();

        $this->registerDeploymentEnded(
            $this->server->present()->deployment_completed_message,
            $rc,
            $changes,
            $errors
        );
    }

    /**
     * Only one project server can be deployed at a time. Supervisord can
     * spawn multiple instances, we don't want to block other projects
     * from deploying so we need this additional check to make sure
     * the project isn't running. A simple release will sufffice
     *
     * @return bool  true if the job should be postponed, false to continue.
     */
    public function postponeIfNeeded()
    {
        if ($this->server->project->is_deploying) {
            \Log::debug("Releaseing job for {$this->server->name} back into the queue.");
            $this->release((int)env('POSTPONMENT_INTERVAL', 30));
            return true;
        }
        return false;
    }

    /**
     * Prepare The Server
     */
    private function prepareServer()
    {
        $branch = data_get($this->options, 'branch', null);
        logger("Preparing Server", [$branch, $this->server->branch]);
        if (!empty($branch) && $branch !== $this->server->branch ) {
            logger("Updating Server Branch: {$branch}");

            $this->server->update(['branch' => $branch]);
        }

        $this->server->updateGitInfo();
    }

    /**
     * Restore Server if needed
     */
    private function restoreServerIfNeeded()
    {
        $use_branch_in_future = data_get($this->options, 'use_branch_in_future', false);
        $branch = $this->original_branch;
        logger("Checking For Server Restore", [$branch, $this->server->branch, $use_branch_in_future]);

        if( ! $use_branch_in_future && $branch !== $this->server->branch) {
            logger("Restoring Server Branch: {$branch}");
            $this->server->update(['branch' => $branch]);
        }
    }

    /**
     * Fire off events to indicate the deployment has started
     *
     * @param  string $message The on start message
     */
    private function registerDeploymentStarted(string $message)
    {
        $this->server->is_deploying = true;
        $this->deployment_started = \Carbon::now();

        event(new DeploymentStarted($this->server, $message));
    }

    /**
     * Fire off events to indicate the deployment has completed
     *
     * @param  string $message The on start message
     */
    private function registerDeploymentEnded(string $message, int $rc, array $changes = [], array $errors = [])
    {
        event(new DeploymentEnded($this->server, $message, $errors, $rc===0));
        $history = $this->saveHistory($rc, $changes, $errors);

        $this->server->is_deploying = false;
        $this->server->notify(new DeploymentComplete($this->server, $history));
    }

    /**
     * Send a message to the broadcast channel
     *
     * @param  string $message message to send
     * @param  array  $errors  Array of errors
     */
    private function sendMessage($message, $errors = [], $progress=0, $stage=null)
    {
        // Don't send empty progress messages...
        if (empty($message)) {
            return;
        }

        event(new DeploymentProgress($this->server, compact('message', 'errors', 'progress', 'stage')));
    }

    /**
     * Store the Deployment History
     *
     * @param  int code $rc TaskWrapper exit code.
     * @param integer $rc
     */
    private function saveHistory($rc, $changes, $errors)
    {
        $history = new History();

        $history->server_id = $this->server->id;
        $history->user_name = $this->user_name;
        $history->project_id = $this->server->project->id;

        $history->from_commit = $this->fromCommit ?: "Beginning of time.";
        $history->to_commit = $this->toCommit ?: $this->server->last_deployed_commit;

        $history->details = [
            'changes' => $changes,
            'errors' => $errors,
        ];

        $history->success = ($rc == 0);
        $history->save();

        return $history;
    }

    public function failed()
    {
        $this->registerDeploymentEnded($this->server->present()->deployment_failed_message, -1);
        \Log::error("!!! JOB FAILED: {$this->server->name} !!!");
    }
}
