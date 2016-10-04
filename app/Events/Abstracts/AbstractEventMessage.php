<?php

namespace App\Events\Abstracts;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Log;

abstract class AbstractEventMessage extends AbstractEchoEvent
{

    protected $channel_id;

    public $message;
    public $errors;
    public $type;

    /**
     * Create a new event instance.
     *
     * @param  $data configuration data for the event
     * @return void
     */
    public function __construct($data)
    {
        $this->message = $data['message'];
        $this->errors = $data['errors'];

        if (isset($data['type'])) {
            $this->type = $data['type'];
        }

        if (isset($data['channel_id'])) {
            $this->channel_id = $data['channel_id'];
        }
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel($this->channel_id);
    }
}
