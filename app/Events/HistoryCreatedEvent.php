<?php

namespace App\Events;

use App\Events\Abstracts\AbstractEchoEvent;
use App\Models\History;
use Illuminate\Broadcasting\PrivateChannel;

final class HistoryCreatedEvent extends AbstractEchoEvent
{
    /**
     * History object
     * @var \App\Models\History
     */
    public $history;

    /**
     * Create a new event instance.
     *
     * @param  $data configuration data for the event
     * @return void
     * @return void
     */
    public function __construct(History $history)
    {
        $this->history = $history;
    }

    public function broadcastWith()
    {
        $this->history->load('server');
        return [
            'history' => $this->history,
        ];
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel($this->history->project->channel_id);
    }
}
