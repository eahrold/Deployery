<?php

namespace App\Services\SSH;

use App\Services\SSH\SFTP;
use Collective\Remote\SecLibGateway as Gateway;
// use phpseclib\Net\SFTP;

class SecLibGateway extends Gateway
{
    /**
     * Get the underlying SFTP connection.
     *
     * @return \phpseclib\Net\SFTP
     */
    public function getConnection()
    {
        if ($this->connection) {
            return $this->connection;
        }
        return $this->connection = new SFTP($this->host, $this->port, $this->timeout);
    }
}
