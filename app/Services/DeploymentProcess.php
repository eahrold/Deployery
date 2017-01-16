<?php

namespace App\Services;

use App\Models\Server;
use App\Services\Git\GitPreparer;

class DeploymentProcess
{
    private $errors = [];

    private $server;

    private $callback;

    private $errorCallback;

    private $uploaded = [];

    private $removed = [];

    /**
     * Is the operation canceled
     *
     * @var boolean
     */
    private $canceled = false;

    public function __construct(Server $server, \Closure $callback = null)
    {
        $this->server = $server;

        $this->callback = $callback ?: function ($line) {
            \Log::debug("Deployment Process Message: {$line}");
        };

        $this->errorCallback = function ($line) {
            \Log::error("Deployment Process Error: {$line}");
        };
    }

    /**
     * Set the Callback
     *
     * @param \Closure $callback Callback
     *
     * @return  Current DeploymentProcess object
     */
    public function setCallback(\Closure $callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * Set the Error callback
     *
     * @param \Closure $callback Error callback
     *
     * @return  Current DeploymentProcess object
     */
    public function setErrorCallback(\Closure $callback)
    {
        $this->errorCallback = $callback;
        return $this;
    }

    /**
     * Deploy the server
     *
     * @param  string   $to       The ending commit
     * @param  mixed    $from     The beginning commit
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    public function deploy($to, $from = null)
    {
        $this->callback($from ? "Deploying from {$from} to {$to}.\n" : "Deploying entire repo...");

        $this->callback("Preparing Repo for deploy.\n");

        $this->prepareGitRepo($to);

        $this->callback("Getting changed files.\n");

        // If "from" was not supplied, then grab
        // the repo from the beginning of time.
        $changes = $from ? $this->server->git_info->changes($from, $to) : $this->server->git_info->all();

        $status = 0;
        $actions = [
            $this->runPreInstallScripts(),
            $this->upload($changes['changed']),
            $this->remove($changes['removed']),
            $this->uploadConfigFiles(),
            $this->runPostInstallScripts(),
            $this->updateServerLastCommit($to)
        ];

        foreach ($actions as $action) {
            $status = $action;
            if ($this->canceled || $status !== 0) {
                break;
            }
        }
        return $status;
    }

    /**
     * Cancel all running deployment processes
     *
     * @return void
     */
    public function cancel()
    {
        $this->canceled = true;
    }

    public function getChanges () {
        return [
            'uploaded' => $this->uploaded,
            'removed' => $this->removed
        ];
    }

    public function getErrors () {
        return $this->errors;
    }

    /**
     * Updates the Server's last commit property
     *
     * @param  string $to the deployed commit
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    private function updateServerLastCommit(string $to)
    {
        $this->server->last_deployed_commit = $to;
        $this->server->save();
        return 0;
    }

    /**
     * Upload a list of files
     *
     * @param  array  $files Local files to upload to remote
     *
     * @return int    This will return 0 for success, anything else indicated a failure.
     */
    private function upload(array $files)
    {
        $file_count = count($files);
        $this->callback("{$file_count} files need uploading".PHP_EOL);

        $previous_dir = "";
        foreach ($files as $idx => $file) {
            $local_file = "{$this->server->repo}/{$file}";
            $remote_file = "{$this->server->deployment_path}/{$file}";

            // SFTP won't automatically create the directory so we need to check
            // and do it ourselves.  We hold on to the previous parent directory
            // to minimize the number of times the remote is queried.
            $pardir = dirname($remote_file);
            if ($pardir != $previous_dir) {
                if (!$this->server->connection->exists($pardir)) {
                    // We need to access the SFTP Connection directly.
                    $this->server->connection->getGateway()->getConnection()->mkdir($pardir, -1, true);
                }
                $previous_dir = $pardir;
            }

            if ($this->server->connection->put($local_file, $remote_file)) {
                $this->uploaded['success'][] = $file;
            } else {
                $this->uploaded['failed'][] = $file;
                $this->errors[] = "Could not upload {$file}";
            }

            $percent = (int)(($idx / $file_count) * 100);
            $this->callback("[{$percent}%] Uploaded {$remote_file}".PHP_EOL);
        }
        return 0;
    }

    /**
     * Remove a list of files from the server
     *
     * @param  array  $files  Local files to upload to remote
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    private function remove($files)
    {
        $file_count = count($files);
        $this->callback("{$file_count} files need removed".PHP_EOL);

        foreach ($files as $file) {
            $file_path = "{$this->server->deployment_path}/$file";
            if ($this->server->connection->exists($file_path)) {
                if($this->server->connection->delete($file_path)) {
                    $this->removed['success'][] = $file;
                } else {
                    $this->errors[] = "Could not remove {$file}";
                    $this->removed['failed'][] = $file;
                }
                $this->callback("Removed {$file_path}".PHP_EOL);
            }
        }
        return 0;
    }

    /**
     * Upload configuration files
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    private function uploadConfigFiles()
    {
        $status = $this->server->configs->count() == 0;
        foreach ($this->server->configs as $config) {
            $path = "{$this->server->deployment_path}/{$config->path}";
            $contents = $config->contents;
            $status = $this->server->connection->putString($path, $contents);
            if ($status) {
                $this->callback("Successfully wrote config file to {$config->path}.");
            } else {
                $msg = "There was a problem writing to {$path} {$status}.";
                $this->errors[] = $msg;
                $this->errorCallback($msg);
            }
        }
        return $status ? 0 : -1;
    }

    /**
     * Run pre-install scripts
     *
     * @param Server $server
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    private function runPreInstallScripts()
    {
        $this->callback("Starting Preinstall Scripts");
        $scripts = $this->server->pre_install_scripts;
        return $this->runScripts($scripts);
    }

    /**
     * Run post-install scripts
     *
     * @param Server $server
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    private function runPostInstallScripts()
    {
        $this->callback("Starting Postinstall Scripts");
        $scripts = $this->server->post_install_scripts;
        return $this->runScripts($scripts);
    }

    /**
     * Execute scripts on server
     *
     * @param  array  $scripts  Array of scripts to execute
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    private function runScripts($scripts)
    {
        $status = 0;
        $connection = $this->server->connection;
        foreach ($scripts as $script) {
            $this->callback("Running {$script->description}");
            $scriptContents = $script->parse($this->server);

            if (empty($scriptContents)) {
                continue;
            }

            $lines = explode(PHP_EOL, $scriptContents);

            $commands = array_map(function ($i) {
                return trim($i);
            }, $lines);

            $commands = array_filter($commands, function ($i) {
                return !empty($i);
            });

            $connection->run($commands, $this->callback);

            $status = $connection->status();
            if ($status !== 0) {
                $msg = "Install Script '{$script->description}' failed. [code: {$status}]";
                $this->errors[] = $msg;
                $this->callback($msg);
                if ($script->stop_on_failure) {
                    return $status;
                }
            }
        }
        return $status;
    }

    /**
     * Prepare local git repo for deployment, fetch and pull remote data,
     * do a hard reset, and clean.
     *
     * @param  Server $server   Server with the properties to configure
     * @param  string $toCommit Commit sha to reset to
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    private function prepareGitRepo(string $toCommit)
    {
        $preparer = new GitPreparer();
        $key = $this->server->project->user->auth_key;
        $repo = $this->server->repo;
        $branch =  $this->server->branch;

        $preparer->withPubKey($key);
        return $preparer->prepare($repo, $branch, $toCommit, function ($line) {
            \Log::debug("Preparing Repo: {$line}");
        });
    }


    //----------------------------------------------------------
    // Magic Methods
    //-------------------------------------------------------

    /**
     * Determine if the method is a callable method
     *
     * @param  string  $name Name of the property
     *
     * @return boolean true if the property is whitelisted as callable, false otherwise.
     */
    private function isCallable($name)
    {
        return in_array($name, ['callback', 'errorCallback']);
    }

    public function __call($name, $args)
    {
        if ($this->isCallable($name)) {
            return call_user_func_array($this->$name, $args);
        }
        return parent::__call($name, $args);
    }
}
