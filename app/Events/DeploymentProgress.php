<?php

namespace App\Events;

use App\Events\Abstracts\AbstractEventMessage;
use App\Models\Server;

final class DeploymentProgress extends AbstractEventMessage
{

    /**
     * Name of the server
     * @var string
     */
    public $server_name;

    /**
     * Id of the server
     * @var int
     */
    public $server_id;

    /**
     * Progess
     * @var int
     */
    public $progress;

    /**
     * Progess
     * @var int
     */
    public $stage;

    /**
     * Event indicating deployment ended
     *
     * @param Server $server  Server being deployed
     * @param   $data configuration data for the event
     * @return  void
     */
    public function __construct(Server $server, $data)
    {
        parent::__construct($data);

        $this->progress = $data['progress'];
        $this->stage = $data['stage'];

        $this->channel_id = $server->project->channel_id;
        $this->server_id = $server->id;
        $this->server_name = $server->name;
    }
}
