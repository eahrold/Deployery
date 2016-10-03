<?php

namespace App\Jobs;

use App\Events\RepositoryCloneMessage;
use App\Jobs\Job;
use App\Models\Project;
use App\Services\GitCloner;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\ProcessBuilder;

class RepositoryClone extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Project Object
     * @var App\Models\Project
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
        $this->sendMessage(
            "Creating cache of repository...",
            self::STARTING
        );

        $project = $this->project;
        $project->is_cloning = true;

        $cloner = (new GitCloner())->withPubKey($project->user->auth_key);

        $callback = function ($message) {
            $this->sendMessage($message, self::PROGRESS);
            $this->project->is_cloning = true;
        };

        $cloner->cloneRepo(
            $project->repo,
            $project->fileStore(),
            $project->local_repo_name,
            $callback
        );

        $project->is_cloning = false;
        $this->sendMessage('The project is ready.', self::COMPLETE);
    }

    /**
     * Send a message to the broadcast socket
     * @param  string $message message to send
     * @param  const  $type    string const describing the message
     * @param  array  $errors  Array of errors
     */
    private function sendMessage($message, $type, $errors = [])
    {
        // Don't send empty progress messages...
        if (($type == self::PROGRESS) && empty($message)) {
            return;
        }

        event(new RepositoryCloneMessage([
            'message'=>$message,
            'errors'=>$errors,
            'type' => $type,
            'channel_id'=>$this->channel(),
            'project_name'=>$this->project->name
        ]));
    }

    public function failed()
    {
        $message = "Cloning {$this->project->repo} failed.";
        $this->sendMessage($message, self::FAILED);
        $this->project->is_cloning = false;
        Log::error("[Job Failed] {$message}");
    }
}
