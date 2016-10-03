<?php

namespace App\Console\Commands\Deployery;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class QueueClones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployery:queue:clones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen for the clones queue.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('queue:listen', ['--queue' => 'clones', '--timeout' => '300']);
    }
}
