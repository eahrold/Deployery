<?php

namespace App\Events;

use App\Events\Abstracts\AbstractEventMessage;

final class RepositoryCloneProgress extends AbstractEventMessage
{

    /**
     * Name of the project
     * @var string
     */
    public $project_name;

    /**
     * Create a new event instance.
     *
     * @param  $data configuration data for the event
     * @return void
     */
    public function __construct($data)
    {
        parent::__construct($data);
        $this->project_name = $data['project_name'];
    }
}
