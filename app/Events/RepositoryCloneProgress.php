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
     * Event for repo clone progress
     *
     * {@inheritdoc}
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->project_name = $data['project_name'];
    }
}
