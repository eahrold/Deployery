<?php

namespace App\Events;

use App\Events\Abstracts\AbstractEventMessage;

final class RepositoryCloneStarted extends AbstractEventMessage
{

    /**
     * Event for repo clone started.
     * {@inheritdoc}
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
