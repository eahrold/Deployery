<?php

namespace App\Console\Commands\Deployery;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class QueueDeployments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployery:queue:deployments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen on the deployments queue.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('queue:listen', ['--queue' => 'deployments', '--timeout' => '300']);
    }
}
