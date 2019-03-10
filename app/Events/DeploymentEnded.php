<?php

namespace App\Events;

use App\Events\Abstracts\AbstractServerEvent;
use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

final class DeploymentEnded extends AbstractServerEvent implements ShouldQueue
{
    use Queueable;

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
     * Errors
     * @var boolean
     */
    public $success;

    /**
     * Event indicating deployment ended
     *
     * @param Server $server  Server being deployed
     * @param string $message Completed messages
     * @param array  $errors  List any errors
     *
     * @return  void
     */
    public function __construct(Server $server, string $message, array $errors = [], $success=true)
    {
        parent::__construct($server);
        $this->message = $message;
        $this->errors = $errors;
        $this->success = $success;
    }
}
