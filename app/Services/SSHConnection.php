<?php

namespace App\Services;

use Collective\Remote\Connection;

class SSHConnection extends Connection {

    const CONNECTION_STATUS_CANNOT_WRITE_TO_PATH = -3;
    const CONNECTION_STATUS_INVALID_PATH = -2;
    const CONNECTION_STATUS_FAILURE = -1;
    const CONNECTION_STATUS_UNKNOWN = 0;
    const CONNECTION_STATUS_SUCCESS = 1;

    /**
     * Upload a local file to the server.
     *
     * @param string $local
     * @param string $remote
     *
     * @return bool
     *
     * NOTE: This is extended for two primary reasons. First
     * the Connection interface doesn't return a bool on put,
     * second, I've found some issues where zero byte files
     * hand on put, so a delete/touch is required to acomplish
     * the equivelant.
     */
    public function put($local, $remote)
    {
        if (filesize($local)) {
            return $this->getGateway()->getConnection()->put($local, $remote);
        } else {
            \Log::info("Found zero byte file {$local}");
            if ($this->exists($remote) && !$this->delete($remote)) {
                return false;
            }
            return $this->getGateway()->getConnection()->touch($remote);
        }
    }

    /**
     * Upload a string to to the given file on the server.
     *
     * @param string $remote
     * @param string $contents
     *
     * @return bool
     */
    public function putString($remote, $contents)
    {
        return $this->getGateway()->getConnection()->put($remote, $contents);
    }
}