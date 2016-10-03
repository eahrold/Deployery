<?php

namespace App\Events;

use App\Events\Abstracts\AbstractServerEvent;
use App\Models\Server;

final class DeploymentEnded extends AbstractServerEvent
{

    /**
     * Message
     * @var string
     */
    public $message;

    /**
     * Errors
     * @var array
     */
    public $errors;

    /**
     * Event indicating deployment ended
     *
     * @param Server $server  Server being deployed
     * @param string $message Completed messages
     * @param array  $errors  List any errors
     * @return  void
     */
    public function __construct(Server $server, string $message, array $errors = [])
    {
        parent::__construct($server);
        $this->message = $message;
        $this->errors = $errors;
    }
}
