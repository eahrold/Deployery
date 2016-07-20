<?php

namespace App\Jobs;

use App\Events\ServerDeploymentEvent;
use App\Jobs\Job;
use App\Models\Server;
use App\Services\SSHKeyer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\ProcessBuilder;

class ServerDeploy extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    const COMPLETE = 'complete';
    const PROGRESS = 'progress';
    const STARTING = 'starting';

    private $server;
    private $socket;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->socket = 'deployment_'.$server->project->id;
    }

    public function socket(){
        return $this->socket;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sendMessage("Starting server deployment...", self::STARTING);

        $envoy = new EnvoyWrapper();
        $envoy->deploy($server, function($message){
            $this->sendMessage($message, self::PROGRESS);
        });

        $this->sendMessage('Deployment Completed', self::COMPLETE);
    }

    private function sendMessage($message, $type, $errors=[]){
        event(new ServerDeploymentEvent([
            'message'=>$message,
            'errors'=>$errors,
            'type' => $type,
            'channel_id'=>$this->socket(),
        ]));
    }
    public function failed()
    {
        $this->sendMessage('Job failed', self::COMPLETE);
        Log::error('Job Is failing...');
    }


}
