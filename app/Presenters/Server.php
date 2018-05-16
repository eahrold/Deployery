<?php

namespace App\Presenters;

use App\Services\SSHConnection;


/**
 * Server presenter class
 */
class Server extends Presenter
{

    public function deployment_started_message()
    {
        return "Queued deployment for <strong>$this->name</strong> ({$this->entity->hostname})";
    }

    public function deployment_completed_message()
    {
        return "Deployment complete to <strong>$this->name</strong> ({$this->entity->hostname})";
    }

    public function deployment_failed_message()
    {
        return "Deployment failed for <strong>$this->name</strong> ({$this->entity->hostname})";
    }

    public function connection_status_message()
    {
        $message = '';

        switch ($this->entity->successfully_connected) {
            case SSHConnection::CONNECTION_STATUS_SUCCESS:
                $message = "Successfully connected to the server";
                break;
            case SSHConnection::CONNECTION_STATUS_UNKNOWN:
                $message = "The connection status is unknown";
                break;
            case SSHConnection::CONNECTION_STATUS_INVALID_PATH:
                $message = "The path {$this->deployment_path} doesn't exist on the server";
                break;
            case SSHConnection::CONNECTION_STATUS_CANNOT_WRITE_TO_PATH:
                $message = "The user doesn't have write access to {$this->deployment_path}";
                break;
            case SSHConnection::CONNECTION_STATUS_FAILURE:
                $message = "Failed to connected to the server, check the username and ";
                if($this->entity->use_ssh_key) {
                    $message .= "make sure to add the ssh key to the user's ~/.ssh/authorized_keys file";
                } else {
                    $message .= 'password';
                }
                break;
            default:
                break;
        }
        return $message;
    }


    public function connection_color()
    {
        $color = '';
        switch ($this->entity->successfully_connected) {
            case SSHConnection::CONNECTION_STATUS_SUCCESS:
                $color = "green";
                break;
            case SSHConnection::CONNECTION_STATUS_UNKNOWN:
                $color = "yellow";
                break;
            case SSHConnection::CONNECTION_STATUS_CANNOT_WRITE_TO_PATH:
            case SSHConnection::CONNECTION_STATUS_INVALID_PATH:
            case SSHConnection::CONNECTION_STATUS_FAILURE:
            default:
                $color = "red";
                break;
        }
        return $color;
    }
}
