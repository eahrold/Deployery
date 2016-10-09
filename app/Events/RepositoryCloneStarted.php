<?php

namespace App\Events;

use App\Events\Abstracts\AbstractEventMessage;

final class RepositoryCloneStarted extends AbstractEventMessage
{


    /**
     * Create a new event instance.
     *
     * @param  $data configuration data for the event
     */
    public function __construct($data)
    {
        parent::__construct($data);
    }
}
