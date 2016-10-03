<?php

namespace App\Console\Commands\Deployery;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class QueueProgress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployery:queue:progress';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen on the progress/default queue.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('queue:work', ["--sleep"=>'3']);
    }
}
