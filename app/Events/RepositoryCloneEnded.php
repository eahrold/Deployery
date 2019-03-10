<?php

namespace App\Events;

use App\Events\Abstracts\AbstractEventMessage;

final class RepositoryCloneEnded extends AbstractEventMessage
{

    /**
     * Exit status of the clone process.
     *
     * @var integer
     */
    public $success;

    /**
     * The Size of the newly cloned repo
     * @var integer
     */
    public $repo_size;

    /**
     * Event for Repo Clone ended
     * {@inheritdoc}
     *
     * @param integer $success   Retrun Code
     * @param integer $repo_size Size Fo the Repo
     */
    public function __construct(array $data, $success=0, $repo_size=0)
    {
        parent::__construct($data);
        $this->success = $success;
        $this->repo_size = $repo_size;
    }
}
