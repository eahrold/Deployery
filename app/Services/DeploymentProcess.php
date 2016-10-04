<?php

namespace App\Services;

use App\Models\Server;

class DeploymentProcess
{
    private $manager;
    private $errors;
    private $callback;

    /**
     * Deploy the server
     *
     * @param  Server $server   [description]
     * @param  mixed  $from     [description]
     * @param  mixed  $to       [description]
     * @param         \Closure $callback [description]
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    public function deploy(Server $server, $from = null, $to = null, $callback = null)
    {
        $callback = $callback ?: function($line) {
            echo $line;
        };
        $to = $to ?: $server->git_info->newest_commit['hash'];

        $callback($from ? "Deploying from {$from} to {$to}.\n" : "Deploying entire repo...");

        $callback("Preparing Repo for deploy.\n");
        $this->prepareGitRepo($server, $to);

        $callback("Getting changed files.\n");

        // If "from" was not supplied, then grab
        // the repo from the beginning of time.
        $changes = $from ? $server->git_info->changes($from, $to) : $server->git_info->all();

        $status = 0;
        $actions = [
            $this->runPreInstallScripts($server, $callback),
            $this->upload($changes['changed'], $server, $callback),
            $this->remove($changes['removed'], $server, $callback),
            $this->uploadConfigFiles($server, $callback),
            $this->runPostInstallScripts($server, $callback),
            $this->updateServerLastCommit($server, $to)
        ];

        foreach ($actions as $action) {
            $status = $action;
            if ($status !== 0) {
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
        if ($this->manager && $this->manager->isRunning()) {
            $this->manager->killall();
        }
    }

    /**
     * Updates the Server's last commit property
     *
     * @param  Server   $server the server to update
     * @param  string   $to     the deployed commit
     *
     * @return integer
     */
    private function updateServerLastCommit(Server $server, string $to)
    {
        $server->last_deployed_commit = $to;
        $server->save();
        return 0;
    }

    /**
     * Rsync implementation of remote sync (depricated)
     *
     * @param  array  $files          Local files to upload to remote
     * @param  Server $server         Server to upload the files
     * @param  function $callback      File uploaded callback,
     * @param  function $errorCallback Error callback
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    private function rsync(array $files, Server $server, $callback, $errorCallback = null)
    {
        $dest = "{$server->username}@{$server->hostname}:{$server->deployment_path}";
        $src = $server->repo;

        // Touch deployment path.
        $server->connection->run(["mkdir -p {$server->deployment_path}"]);

        // Setup Exclude Context for Rsync
        $excludes = [
            '.git',
        ];

        $excludeContext = ' ';
        foreach ($excludes as $exclude) {
            $excludeContext .= "--exclude='{$exclude}' ";
        }

        $tasks = [
            "rsync -rltgoDzv {$excludeContext} --filter=':- .gitignore' {$src}/ {$dest}"
        ];

        $this->manager = new ProcessManager();

        $_callback = function($line) use ($callback) {
            $silence = [
                'sent',
                'total'
            ];
            $l = current(explode(" ", $line));
            if (!in_array($l, $silence)) {
                $callback("Uploaded $line");
            }
        };

        return $this->manager->setWorkingDirectory($server->project->repoPath())
                    ->setStdOut($_callback)
                    ->setStdErr($errorCallback ?: $_callback)
                    ->run($tasks);
    }

    /**
     * Upload a list of files
     *
     * @param  array  $files          Local files to upload to remote
     * @param  Server $server         Server to upload the files
     * @param  function $callback      File uploaded callback,
     * @param  function $errorCallback Error callback
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    private function upload(array $files, Server $server, $callback, $errorCallback = null)
    {
        $file_count = count($files);
        $callback("{$file_count} files need uploading".PHP_EOL);

        $previous_dir = "";
        foreach ($files as $idx => $file) {
            $local_file = "{$server->repo}/{$file}";
            $remote_file = "{$server->deployment_path}/{$file}";

            // SFTP won't automatically create the directory so we need to check
            // and do it ourselves.  We hold on to the previous parent directory
            // to minimize the number of times the remote is queried.
            $pardir = dirname($remote_file);
            if ($pardir != $previous_dir) {
                if (!$server->connection->exists($pardir)) {
                    // We need to access the SFTP Connection directly.
                    $server->connection->getGateway()->getConnection()->mkdir($pardir, -1, true);
                }
                $previous_dir = $pardir;
            }

            $server->connection->put($local_file, $remote_file);
            $percent = (int)(($idx / $file_count) * 100);
            $callback("[{$percent}%] Uploaded {$remote_file}".PHP_EOL);
        }
        return 0;
    }

    /**
     * Remove a list of files from the server
     * @param  array  $files          Local files to upload to remote
     * @param  Server $server         Server to upload the files
     * @param  function $callback      File removed callback,
     * @param  function $errorCallback Error callback
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    private function remove($files, $server, $callback, $errorCallback = null)
    {
        $file_count = count($files);
        $callback("{$file_count} files need removed".PHP_EOL);

        foreach ($files as $file) {
            $file_path = "{$server->deployment_path}/$file";
            if ($server->connection->exists($file_path)) {
                $server->connection->delete($file_path);
                $callback("Removed {$file_path}".PHP_EOL);
            }
        }
        return 0;
    }

    /**
     * Upload configuration files
     * @param  Server $server         Server to upload the files
     * @param  function $callback      Config file written callback,
     * @param  function $errorCallback Error callback
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    private function uploadConfigFiles(Server $server, $callback = null, $errorCallback = null)
    {
        $status = false;
        $callback = $callback ?: function($msg) {
            \Log::debug($msg);
        };
        $errorCallback = $errorCallback ?: $callback;

        foreach ($server->configs as $config) {
            $path = "{$server->deployment_path}/{$config->path}";
            $contents = $config->contents;
            $status = $server->connection->putString($path, $contents);
            if ($status) {
                $callback("Successfully wrote config file to {$config->path}.");
            } else {
                $errorCallback("There was a problem writing to {$path} {$status}.");
            }
        }
        return $status ? 0 : -1;
    }

    /**
     * @param Server $server
     */
    private function runPreInstallScripts($server, $callback = null, $errorCallback = null)
    {
        $callback("Starting Preinstall Scripts");
        $scripts = $server->pre_install_scripts;
        return $this->runScripts($scripts, $server, $callback, $errorCallback);
    }

    /**
     * @param Server $server
     */
    private function runPostInstallScripts($server, $callback = null, $errorCallback = null)
    {
        $callback("Starting Postinstall Scripts");
        $scripts = $server->post_install_scripts;
        return $this->runScripts($scripts, $server, $callback, $errorCallback);
    }

    /**
     * Execute scripts on server
     * @param  array $scripts        Array of scripts to execute
     * @param  Server $server        Server to execute the scripts on
     * @param  function $callback    Progress callback (std_out)
     * @param  $errorCallback        Error callback (std_err)
     *
     * @return int  This will return 0 for success, anything else indicated a failure.
     */
    private function runScripts($scripts, Server $server, $callback = null, $errorCallback = null)
    {
        $status = 0;
        $connection = $server->connection;
        foreach ($scripts as $script) {
            $callback("Running {$script->description}");
            $scriptContents = $script->parse($server);

            if (empty($scriptContents)) {
                continue;
            }

            $lines = explode(PHP_EOL, $scriptContents);

            $commands = array_map(function($i) {
                return trim($i);
            }, $lines);

            $commands = array_filter($commands, function($i) {
                return !empty($i);
            });
            $connection->run($commands, $callback);

            if ($script->stop_on_failure) {
                $status = $connection->status();
                if ($status !== 0) {
                    $callback("Install Script failed!");
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
    private function prepareGitRepo(Server $server, string $toCommit)
    {
        $tasks = [
            "git fetch origin",
            "git reset --hard origin/{$server->branch}",
            "git pull",
            "git reset --hard {$toCommit}",
            "git clean -xdf",
        ];

        $this->manager = new ProcessManager();

        $key = $server->project->user->auth_key;
        $env = ["GIT_SSH_COMMAND", "ssh -i {$key}"];
        $this->manager->setEnv($env);

        return $this->manager
                    ->setWorkingDirectory($server->repo)
                    ->run($tasks, null, function($line) {
                        \Log::info("Preparing Repo: {$line}");
                    }, true);
    }
}
