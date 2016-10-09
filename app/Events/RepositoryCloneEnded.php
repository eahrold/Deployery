<?php

namespace App\Events;

use App\Events\Abstracts\AbstractEventMessage;

final class RepositoryCloneEnded extends AbstractEventMessage
{

    /**
     * Exit status of the clone process.
     *
     * @var string
     */
    public $success;

    /**
     * The Size of the newly cloned repo
     * @var [type]
     */
    public $repo_size;

    /**
     * Create a new event instance.
     *
     * @param  $data configuration data for the event
     */
    public function __construct($data, $success=0, $repo_size=0)
    {
        parent::__construct($data);
        $this->success = $success;
        $this->repo_size = $repo_size;
    }
}
