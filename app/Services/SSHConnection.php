<?php

namespace App\Services;

use Collective\Remote\Connection;
use phpseclib\Net\SFTP;

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
        if (is_link($local)) {
            $target = readlink($local);
            return $this->getGateway()->getConnection()->symlink($target, $local);
        }

        if (filesize($local)) {
            return $this->getGateway()->getConnection()->put($remote, $local, SFTP::SOURCE_LOCAL_FILE);
        }
        else {
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

    public function validate($path)
    {
        $status = self::CONNECTION_STATUS_SUCCESS;

        if (!$this->exists($path)) {
            $status = self::CONNECTION_STATUS_INVALID_PATH;
        } else {
            $fauxf = strtoupper(md5(uniqid(rand(), true)));
            $fauxPath = "{$path}/{$fauxf}";
            $this->putString($fauxPath, "hello world");

            if (!$this->exists($fauxPath)) {
                $status = self::CONNECTION_STATUS_CANNOT_WRITE_TO_PATH;
            } else {
                $this->delete($fauxPath);
            }
        }

        // If the connection status wasn't caught by any of the
        // above conditons, but there was a failure...
        if ($status == 0 && $this->status() != 0) {
            $status = self::CONNECTION_STATUS_FAILURE;
        }

        return $status;
    }
}