<?php

namespace App\Events;

use App\Events\Abstracts\AbstractServerEvent;
use App\Models\Server;

final class DeploymentStarted extends AbstractServerEvent
{

    /**
     * Message
     * @var string
     */
    public $message;

    /**
     * Event indicating deployment ended
     *
     * @param Server $server  Server being deployed
     * @param string $message Completed messages
     * @return  void
     */
    public function __construct(Server $server, string $message)
    {
        parent::__construct($server);
        $this->message = $message;
    }
}
