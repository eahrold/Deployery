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
     * Abstract Event
     * @param array $data Configuration data for event
     */
    public function __construct(array $data)
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
