<?php

namespace App\Jobs;

use App\Events\RepositoryCloneEnded;
use App\Events\RepositoryCloneProgress;
use App\Events\RepositoryCloneStarted;
use App\Jobs\Job;
use App\Models\Project;
use App\Services\Git\GitCloner;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RepositoryClone extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Project Object
     * @var \App\Models\Project
     */
    private $project;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->project->is_cloning = true;
        $this->sendStartedMessage("Adding repository clone to the queue...");
    }

    public function channel()
    {
        return $this->project->channel_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sendStartedMessage("Creating cache of repository...");
        \Log::debug('Repo Clone started');

        $project = $this->project;
        $cloner = (new GitCloner())->withPubKey($project->user->auth_key);

        $callback = function ($message) {
            // logger($message);
            $this->sendProgress($message);
            $this->project->setIsCloningAttribute(true);
        };

        $success = $cloner->cloneRepo(
            $project->repo,
            $project->fileStore(),
            $project->local_repo_name,
            $callback
        );

        $this->sendCompleteMessage($success, $cloner->getErrors());
    }

    private function sendStartedMessage($message)
    {
        $this->project->setIsCloningAttribute(true);
        $payload = [
            'message' => $message,
            'channel_id' => $this->channel(),
            'project_name' => $this->project->name
        ];
        logger("Clone Started", $payload);
        event(new RepositoryCloneStarted($payload));
    }

    /**
     * Send a message to the broadcast socket
     * @param  string $message message to send
     * @param  string $type    string const describing the message
     * @param  array  $errors  Array of errors
     */
    private function sendProgress($message, $errors = [])
    {
        // Don't send empty progress messages...
        if (empty($message) && empty($errors)) {
            return;
        }
        $payload = [
            'message' => $message,
            'errors' => $errors,
            'channel_id' => $this->channel(),
            'project_name' => $this->project->name
        ];

        event(new RepositoryCloneProgress($payload));
    }

    private function sendCompleteMessage($success, $errors, $message = null)
    {
        $this->project->setIsCloningAttribute(false);

        $message = $message ?: $success ? "Your project has been successfully cloned.":
            "There was an error cloning the repo, if this is a private repo make sure you've added your account SSH key to the repo's host.";

        $payload = [
            'channel_id' => $this->channel(),
            'message' => $message,
            'errors' => $errors,
        ];

        $size = $success ? $this->project->getRepoSizeAttribute(true) : 0;
        logger("Clone Complete", compact('payload', 'success', 'size'));

        event(new RepositoryCloneEnded($payload, $success, $size));
    }

    public function failed()
    {
        $message = "A fatal error occurred while cloning {$this->project->repo}.";
        \Log::error("[Job Failed] {$message}");
        $this->sendCompleteMessage(false, [], $message);
    }
}
