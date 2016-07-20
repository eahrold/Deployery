<?php

namespace App\Console\Commands;

use App\Models\Server;
use App\Services\EnvoyWrapper;
use Illuminate\Console\Command;

class TestEnvoyServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:envoy:server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test an Envoy Server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $server = Server::all()->first();
        $wrapper = new EnvoyWrapper();
        $wrapper->deploy($server, function($message){
            echo $message;
        });
    }
}
