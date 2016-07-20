<?php

namespace App\Services;

use App\Models\Server;
use Collective\Remote\RemoteManager;


class SSHAccess extends RemoteManager
{

    /**
     * Get the configuration for a remote server.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function getConfig($name)
    {
        $server = Server::where('name', $name)->firstOrFail();
        $config = $server->config;
        if (!is_null($config)) {
            return $server->config;
        }
        throw new \InvalidArgumentException("Remote connection [$name] not defined.");
    }

}