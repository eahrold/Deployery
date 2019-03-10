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
     * Event when a history is created
     *
     * @param History $history [description]
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
     * @return PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel($this->history->project->channel_id);
    }
}
