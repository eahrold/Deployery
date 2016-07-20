<?php

namespace App\Events;

use App\App\Model\DeploymentMessage;
use App\Events\Event;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ServerDeploymentEvent extends Event  implements ShouldBroadcast
{
    use SerializesModels;

    public $message;
    public $errors;
    public $type;

    private $channel_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data){
        $this->message = $data['message'];
        $this->errors = $data['errors'];
        $this->type = $data['type'];

        $this->channel_id = $data['channel_id'];
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn(){
        return [$this->channel_id];
    }
}
