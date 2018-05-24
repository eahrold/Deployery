<?php

namespace App\Services;

use App\Models\Server;
use App\Services\Git\GitPreparer;
use Illuminate\Database\Eloquent\Collection;

/**
 * Deployment Process
 *
 * @method string callback() call the callback \Closure property
 * @method string errorCallback() call the errorCallback \Closure property
 */
class DeploymentProcess
{
    const PROGRESS_PREPARING = 0;
    const PROGRESS_PRE_INSTALL = 10;
    const PROGRESS_SYNC_FILES = 20;
    const PROGRESS_SYNC_FILES_COMPLETE = 79;
    const PROGRESS_WRITING_CONFIG = 80;
    const PROGRESS_POST_INSTALL = 90;
    const PROGRESS_COMPLETE = 100;

    const STAGE_PREPARING = 0;
    const STAGE_PRE_INSTALL = 1;
    const STAGE_SYNC_FILES = 2;
    const STAGE_SYNC_FILES_COMPLETE = 3;
    const STAGE_WRITING_CONFIG = 4;
    const STAGE_POST_INSTALL = 5;
    const STAGE_COMPLETE = 6;

    private $stage_progress;

    /**
     * Server
     * @var \App\Models\Server
     */
    private $server;

    /**
     * Std Callback
     * @var \Closure
     */
    private $callback;

    /**
     * Error Callback
     * @var \Closure
     */
    private $errorCallback;

    /**
     * Uploaded Files
     * @var array
     */
    private $uploaded = [];

    /**
     * Removed Files
     * @var array
     */
    private $removed = [];

    /**
     * Errors
     * @var array
     */
    private $errors = [];

    /**
     * Progress
     * @var integer
     */
    private $progress = 0;

    /**
     * Progress
     * @var integer
     */
    private $stage = 0;

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

        $this->stage_progress = [
            static::STAGE_PREPARING => static::PROGRESS_PREPARING,
            static::STAGE_PRE_INSTALL => static::PROGRESS_PRE_INSTALL,
            static::STAGE_SYNC_FILES => static::PROGRESS_SYNC_FILES,
            static::STAGE_SYNC_FILES_COMPLETE => static::PROGRESS_SYNC_FILES_COMPLETE,
            static::STAGE_WRITING_CONFIG => static::PROGRESS_WRITING_CONFIG,
            static::STAGE_POST_INSTALL => static::PROGRESS_POST_INSTALL,
            static::STAGE_COMPLETE => static::PROGRESS_COMPLETE,
        ];
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
    public function deploy($to, $from = null, $script_ids=[])
    {
        $this->sendMessage($from ? "Deploying from {$from} to {$to}.\n" : "Deploying entire repo...");

        $tasks = [
            'prepare' => compact('to'),
            'preInstall' => null,
            'syncFiles' => compact('to', 'from'),
            'writeConfig' => null,
            'postInstall' => compact('script_ids'),
            'complete' => compact('to')
        ];
        $status = $this->runTasksOrFail($tasks);

        $this->sendMessage("Deployment completed with status: {$status}.\n");
        logger("Completed {$this->server->name} Deployment: {$status}");

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

    /**
     * Get a list of the changed files
     * @return array Changes
     */
    public function getChanges () {
        return [
            'uploaded' => $this->uploaded,
            'removed' => $this->removed
        ];
    }

    /**
     * Get the errors that occurred during deployment
     * @return array Errors
     */
    public function getErrors () {
        return $this->errors;
    }

    //----------------------------------------------------------
    // Deployment Blueprint Methods
    //-------------------------------------------------------
    private function prepare($options=[])
    {
        $this->setStage(static::STAGE_PREPARING);
        $this->sendMessage("Preparing Repo for deploy.\n");

        $this->prepareGitRepo($options['to']);
        return 0;
    }

    private function preInstall($options=[])
    {
        $this->setStage(static::STAGE_PRE_INSTALL);
        return $this->runPreInstallScripts();
    }

    private function syncFiles($options=[])
    {
        $this->setStage(static::STAGE_SYNC_FILES);
        $this->sendMessage("Determining changed files.\n");

        $from = $options['from'];
        $to = $options['to'];

        // If "from" was not supplied, then grab
        // the repo from the beginning of time.
        $changes = $from ? $this->server->git_info->changes($from, $to) : $this->server->git_info->all();

        return $this->runTasksOrFail([
            'uploadFiles' => $changes['changed'],
            'removeFiles' => $changes['removed'],
        ]);
    }

    private function writeConfig($options=[])
    {
        $this->setStage(static::STAGE_WRITING_CONFIG);
        return $this->uploadConfigFiles();
    }

    private function postInstall($options=[])
    {
        $this->setStage(static::STAGE_POST_INSTALL);
        $script_ids = $options['script_ids'];

        return $this->runTasksOrFail([
            'runPostInstallScripts' => null,
            'runOneOffScripts' => $script_ids,
        ]);
    }

    private function complete($options=[])
    {
        $this->setStage(static::STAGE_COMPLETE);
        $this->updateServerLastCommit($options['to']);

        return 0;
    }
    //----------------------------------------------------------
    // End Deployment Blueprint Methods
    //-------------------------------------------------------

    private function setStage($stage)
    {
        $this->stage = $stage;
        $this->progress = $this->stage_progress[$stage];
    }
    /**
     * Execute an array of methods, fail
     * @param  array  $tasks array of callable methods
     * @return int    0 for success, any other status code indicating failure.
     */
    private function runTasksOrFail(array $tasks)
    {
        $status = 0;
        foreach ($tasks as $action => $params) {
            $status = $this->{$action}($params);
            if ($this->canceled || $status !== 0) {
                return $this->canceled ? -1 : $status;
            }
        }
        return $status;
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
    private function uploadFiles(array $files)
    {
        $file_count = count($files);

        $this->sendMessage("{$file_count} files need uploading".PHP_EOL);

        $progress_increment = (static::PROGRESS_SYNC_FILES_COMPLETE - $this->progress) / ($file_count+1);

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
            $this->progress = floor($this->progress + $progress_increment);

            $truncated = substr($remote_file, -60);
            $this->sendMessage("<b class='text-info'>Uploaded {$percent}%:</b> ...{$truncated}".PHP_EOL);
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
    private function removeFiles($files)
    {
        $file_count = count($files);
        $this->sendMessage("{$file_count} files need removed".PHP_EOL);

        foreach ($files as $file) {
            $file_path = "{$this->server->deployment_path}/$file";
            $truncated = substr($file, -60);

            if ($this->server->connection->exists($file_path)) {
                if($this->server->connection->delete($file_path)) {
                    $this->removed['success'][] = $file;
                } else {
                    $this->errors[] = "Could not remove {$file}";
                    $this->removed['failed'][] = $file;
                }
                $this->sendMessage("<b class='text-danger'>Removed</b> {$truncated}".PHP_EOL);
            } else {
                $this->sendMessage("<b class='text-danger'>Removed</b> {$truncated}".PHP_EOL);
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
                $this->sendMessage("Successfully wrote config file to {$config->path}.");
            } else {
                $msg = "There was a problem writing to {$path} {$status}.";
                $this->errors[] = $msg;
                $this->errorCallback($msg);
            }
        }
        return $status ? 0 : -1;
    }

    /**
     * Run one-off scripts
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    private function runOneOffScripts($script_ids=[])
    {
        if (!empty($script_ids)) {

            $this->sendMessage("Starting One-Off Scripts");

            $pre = $this->server->pre_install_scripts->pluck('id');
            $post = $this->server->post_install_scripts->pluck('id');
            $unique = $pre->merge($post)->values()->all();

            $scripts = $this->server->oneOffScripts()
                            ->whereIn('id', $script_ids)
                            ->whereIn('id', $unique , 'and', true)
                            ->get();

            return $this->runScripts($scripts);
        }
        return 0;
    }

    /**
     * Run pre-install scripts
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    private function runPreInstallScripts()
    {
        $this->sendMessage("Starting Preinstall Scripts");
        $scripts = $this->server->pre_install_scripts;
        return $this->runScripts($scripts);
    }

    /**
     * Run post-install scripts
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    private function runPostInstallScripts()
    {
        $this->sendMessage("Starting Postinstall Scripts");
        $scripts = $this->server->post_install_scripts;
        return $this->runScripts($scripts);
    }

    /**
     * Execute scripts on server
     *
     * @param  Collection  $scripts  Collection of scripts to execute
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    private function runScripts(Collection $scripts)
    {
        $status = 0;
        $connection = $this->server->connection;

        foreach ($scripts as $script) {
            $this->sendMessage("Running {$script->description}");
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

            $connection->run($commands, function($message){
                $this->sendMessage($message);
            });

            $status = $connection->status();
            if ($status !== 0) {
                $msg = "Install Script '{$script->description}' failed. <b class='text-danger'>code: {$status}</b>";
                $this->errors[] = $msg;
                $this->sendMessage($msg);
                if ($script->stop_on_failure) {
                    return -1;
                }
            }
        }
        return $status;
    }

    /**
     * Prepare local git repo for deployment, fetch and pull remote data,
     * do a hard reset, and clean.
     *
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
            $this->sendMessage($line);
        });
    }

    private function sendMessage($message) {
        $this->callback($message, $this->progress, $this->stage);
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
