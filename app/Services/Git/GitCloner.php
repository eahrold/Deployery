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
     *
     * @param  string $repo clone url
     * @param  string $dir  directory where the repo should be cloned (process CWD)
     * @param  string $name the name of the repo being cloned
     * @param  \Closure $callback
     * @return bool Success if the repo was suceesfully cloned.
     */
    public function cloneRepo(string $repo, string $dir, string $name, \Closure $callback = null)
    {
        $this->callback = $callback;

        if (!file_exists($dir) && !mkdir($dir, 0770, true)) {
            $this->sendMessage("Failed to create a directory at {$dir}");
            return false;
        }

        $task = "clone {$repo} {$name}";
        $builder = new GitProcessBuilder($dir);

        $builder->withPubKey($this->pub_key)
                ->setTimeout(300)
                ->setTask($task);

        $process = $builder->getProcess();
        $process->run(function ($type, $buffer) {
            if (!empty($buffer)) {
                $this->sendMessage($buffer);
            }
        });
        return ($process->getExitCode() === 0);
    }

    private function sendMessage(string $buffer, $error = false, $firstLineOnly = true)
    {
        if ($this->callback) {
            // The [K character is used to clear the terminal
            // We need to strip it out from the raw string
            $buffer = str_replace("[K", PHP_EOL, $buffer);
            foreach (explode(PHP_EOL, $buffer) as $line) {
                call_user_func($this->callback, trim($line));
                if ($firstLineOnly) {
                    return;
                }
            }
        }
    }
}
