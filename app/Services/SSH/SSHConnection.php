<?php

namespace App\Services\SSH;

use App\Services\SSH\SecLibGateway;
use Collective\Remote\Connection;
use Collective\Remote\GatewayInterface;
use Illuminate\Filesystem\Filesystem;
use League\Flysystem\Filesystem as FlysystemFS;
use League\Flysystem\Sftp\SftpAdapter;
use phpseclib\Net\SFTP;
use phpseclib\Net\SSH2;


class SSHConnection extends Connection {

    const CONNECTION_STATUS_CANNOT_WRITE_TO_PATH = -3;
    const CONNECTION_STATUS_INVALID_PATH = -2;
    const CONNECTION_STATUS_FAILURE = -1;
    const CONNECTION_STATUS_UNKNOWN = 0;
    const CONNECTION_STATUS_SUCCESS = 1;

    private $filesystem = null;

    public function __construct($name, $host, $username, array $auth, GatewayInterface $gateway = null, $timeout = 10)
    {

        $gateway = $gateway ?? new SecLibGateway($host, $auth, new Filesystem(), $timeout);
        parent::__construct($name, $host, $username, $auth, $gateway , $timeout);

        // $this->filesystem = new $this->createSftpFileSystem($name, $host, $username, $auth, $timeout);
    }

    /**
     * Create an League\Flysystem\Filesystem For Sftp
     *
     * @param  string  $name     description
     * @param  string  $host     host ip or fqdn
     * @param  string  $username username of logging in user
     * @param  array   $auth     auth
     * @param  integer $timeout  timeout
     *
     * @return League\Flysystem\Filesystem
     */
    private function createSftpFileSystem(string $name, string $host, string $username, array $auth, $timeout=300) {
        $params = [
            'host' => $host,
            'port' => 22,
            'username' => $username,
            'timeout' => $timeout,
            'root' => '/',
            // 'password' => 'password',
            // 'privateKey' => 'path/to/or/contents/of/privatekey',
            // 'directoryPerm' => 0755
        ];

        if ($password = data_get($auth, 'password')) {
            $params['password'] = $password;
        }
        else if ($key = data_get($auth, 'key')) {
            $params["privateKey"] = $key;
            if($keyphrase = data_get($auth, 'keyphrase')) {
                $params['password'] = $keyphrase;
            }
        }

        $adapter = new SftpAdapter($params);
        $filesystem = new FlysystemFS($adapter);
        $adapter->connect();

        return $filesystem;
    }

    /**
     * Proxy To Get Connection Direclty
     *
     * @return \phpseclib\Net\SFTP
     */
    protected function getGatewayConnection() {
        return $this->getGateway()->getConnection();
    }

    /**
     * Creates a directory.
     *
     * @param string $dir
     *
     * @return bool
     */
    function mkdir($dir, $mode = -1, $recursive = false)
    {
        return $this->getGatewayConnection()->mkdir($dir, $mode, $recursive);
    }

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
            return $this->getGatewayConnection()->symlink($target, $local);
        }

        if (filesize($local)) {
            // $stream = fopen($local, 'r+');
            // logger("Still Connected...", [$this->filesystem->getAdapter()->isConnected()]);
            // $results = $this->filesystem->putStream($remote, $stream);
            // logger("Put Using Stream...", [$results]);
            // fclose($stream);
            // return $results;
            $results = $this->getGatewayConnection()
                            ->put($remote, $local, SFTP::SOURCE_LOCAL_FILE);

            return $results;
        }
        else {
            if ($this->exists($remote)) {
                if(!$this->delete($remote)) {
                    return false;
                }
            }
            return $this->getGatewayConnection()->touch($remote);
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
        return $this->getGatewayConnection()->put($remote, $contents);
        // return $this->filesystem->put($remote, $contents);
    }

    /**
     * Validate the SSH connection
     *
     * @param  string $path a writable directory on the remote
     *
     * @return int  Class const of type CONNECTION_STATUS_x
     */
    public function validate(string $path) : bool
    {

        $status = static::CONNECTION_STATUS_UNKNOWN;

        if (!$this->exists($path)) {
            $status = static::CONNECTION_STATUS_INVALID_PATH;
        } else {
            $fauxf = strtoupper(md5(uniqid(rand(), true)));
            $fauxPath = "{$path}/{$fauxf}";
            $results = $this->putString($fauxPath, "hello world");

            if (!$this->exists($fauxPath)) {
                $status = static::CONNECTION_STATUS_CANNOT_WRITE_TO_PATH;
            } else {
                $status = static::CONNECTION_STATUS_SUCCESS;
                $this->delete($fauxPath);
            }
        }

        // If the connection status wasn't caught by any of the
        // above conditons, but there was a failure...

        $errors = $this->getGatewayConnection()->getErrors();

        if ($status == static::CONNECTION_STATUS_UNKNOWN && !empty($errors)) {
            $status = static::CONNECTION_STATUS_FAILURE;
        }

        return $status;
    }
}