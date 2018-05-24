<?php

namespace App\Services\Git;

use App\Services\Git\Traits\GitAuthenticatable;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Get Git Info for a repo and branch.
 */
class GitCloner
{

    use GitAuthenticatable;

    /**
     * @var GitProcessBuilder
     */
    private $builder;

    /**
     * Event callback
     * @var \Clsoure
     */
    private $callback;

    /**
     * Array of errors from the cloning process
     *
     * @var array
     */
    private $errors;

    /**
     * Get errors that occured during clone process
     *
     * @return array Errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Clone a repo
     * @param  string   $url clone url
     * @param  string   $repo     repo path
     * @param  string   $name the name of the repo being cloned
     * @param  \Closure $callback
     *
     * @return bool Success if the repo was suceesfully cloned.
     */
    public function cloneRepo(string $url, string $repo, string $name, \Closure $callback = null)
    {
        if (!file_exists($repo) && !mkdir($repo, 0770, true)) {
            $this->sendMessage("Failed to create a directory at {$repo}");
            return false;
        }

        $task = "clone --progress {$url} {$name}";
        return $this->runTask($task, $repo, $callback);
    }

    /**
     * Update Repo's URL
     * @param  string        $repo     repo path
     * @param  string        $url      origin url
     *
     * @return bool Success if the repo was suceesfully cloned.
     */
    public function updateRepoUrl(string $repo, string $url)
    {
        $task = "remote set-url origin {$url}";
        return $this->runTask($task, $repo);
    }

    /**
     * Run Internal Task
     * @param  string        $task     task to run
     * @param  string        $repo     path to the repo

     * @param  \Closure|null $callback Callback
     * @return bool Success if the repo was suceesfully cloned.
     */
    private function runTask(string $task, string $repo, \Closure $callback = null)
    {
        $this->callback = $callback;
        $builder = new GitProcessBuilder($repo);

        $builder->withPubKey($this->pub_key)
                ->setTimeout(300)
                ->setTask($task);

        $process = $builder->getProcess();
        $process->run(function ($type, $buffer) {
            if (!empty($buffer)) {
                $this->sendMessage($buffer);
            }
        });

        $success = ($process->getExitCode() === 0);
        $this->errors = $success ? [] : array_filter(
            explode(PHP_EOL, $process->getErrorOutput())
        );
        return $success;
    }

    /**
     * Send Progress Message
     * @param  string  $buffer        stdout/stderr
     * @param  boolean $error         is this an error
     * @param  boolean $firstLineOnly only message first line, to avoid excessive progress
     */
    private function sendMessage(string $buffer, $error = false, $firstLineOnly = true)
    {
        if ($this->callback) {
            // The [K character is used to clear the terminal
            // We need to strip it out from the raw string
            $buffer = str_replace("[K", PHP_EOL, $buffer);
            $buffer = str_replace("\r", PHP_EOL, $buffer);

            foreach (explode(PHP_EOL, $buffer) as $line) {
                call_user_func($this->callback, trim($line));
                if ($firstLineOnly) {
                    return;
                }
            }
        }
    }
}
