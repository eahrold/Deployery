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

        foreach (['errors', 'type', 'channel_id'] as $key) {
            if (isset($data[$key])) {
                $this->{$key} = $data[$key];
            }
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
