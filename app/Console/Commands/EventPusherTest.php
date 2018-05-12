<?php

namespace App\Console\Commands;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Console\Command;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Pusher\Pusher;

class EventPusherTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:pusher:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate your pusher configuration';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line("PUSHER_KEY: ".env('PUSHER_KEY'));
        $this->line("PUSHER_SECRET: ".env('PUSHER_SECRET'));
        $this->line("PUSHER_APP_ID: ".env('PUSHER_APP_ID'));

        broadcast(new PusherTestEvent());
        $this->rawPush();
    }

    public function rawPush()
    {
        $options = array(
            'encrypted' => true
        );

        $pusher = new Pusher(
            env('PUSHER_KEY'),
            env('PUSHER_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $data['message'] = 'Direct Pusher Test Event';

        $response = $pusher->trigger(
            'PusherLibTestPusherEvent',
            'App\Console\Commands\PusherLibTestEvent',
            ['message' => 'Pusher Lib Event Test'],
            null, //<-- Ignored sockets
            true //<-- Debug?
        );

        $this->line("Look at your Pusher's debug console to verify the message was sent.");
        dd($response);
    }
}

class PusherTestEvent implements ShouldBroadcast
{
    use SerializesModels, InteractsWithSockets;

    protected $channel_id;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct()
    {
        $this->channel_id = 'LaravelPusherTestEvent';
        $this->message = 'Laravel Pusher Event Test';
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return [new Channel($this->channel_id)];
    }
}
