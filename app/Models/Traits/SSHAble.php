<?php

namespace App\Models\Traits;

use App\Services\SSHConnection;

trait SSHAble {

    /**
     * Underlying Connection
     * @var [type]
     */
    private $ssh_connection;

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootSSHAble()
    {
        static::updating(function($model) {
            // A list of properties that if changed, require revalidating of the server connection
            $check = ['hostname', 'username', 'password', 'port', 'deployment_path'];
            if ($model->isDirty($check)) {
                $model->successfully_connected = SSHConnection::CONNECTION_STATUS_UNKNOWN;
            }
        });
    }

    /**
     * Get Credentials for the connection auth
     *
     * @return array credentials
     */
    private function getConnectionAuth()
    {
        if ($this->use_ssh_key) {
            return ['key' => $this->project->ssh_key,
                    'keyphrase' => ''];
        } else {
            return [
                'password' => $this->password ?: ""
            ];
        }
    }

    /**
     * Getter for the connection attribute
     *
     * @return \App\Services\SSHConnection
     */
    public function getConnectionAttribute()
    {
        if (!$this->ssh_connection) {
            $this->ssh_connection = new SSHConnection(
                $this->slug,
                $this->hostname,
                $this->username,
                $this->getConnectionAuth(),
                null,
                $timeout = 10
            );
        }
        return $this->ssh_connection;
    }

    /**
     * Validate the SSH Connection
     *
     * @return bool true if connection was successful, false otherwise
     */
    public function validateConnection()
    {
        try {
            $status = $this->getConnectionAttribute()
                           ->validate($this->deployment_path);
        } catch (\Exception $e) {
            $status = SSHConnection::CONNECTION_STATUS_FAILURE;
        }

        $this->successfully_connected = $status;
        $this->save();
        return ($status === SSHConnection::CONNECTION_STATUS_SUCCESS);
    }
}
