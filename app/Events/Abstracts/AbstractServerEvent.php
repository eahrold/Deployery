<?php

namespace App\Events\Abstracts;

use App\Models\Server;
use Illuminate\Broadcasting\PrivateChannel;

abstract class AbstractServerEvent extends AbstractEchoEvent
{
    /**
     * @var Server
     */
    public $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel($this->server->project->channel_id);
    }
}
