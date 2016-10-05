<?php

namespace App\Services;

use Symfony\Component\Process\ProcessBuilder;

/**
 * Get Git Info for a repo and branch.
 */
class GitCloner {

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
    public function getErrors(){
        return $this->errors;
    }

    private $pub_key;
    public function withPubKey($pub_key){
        $this->pub_key = $pub_key;
        return $this;
    }

    private $password;
    public function withPassword($password){
        $this->password = $password;
        return $this;
    }

    /**
     * Clone a repo
     *
     * @param  string $repo clone url
     * @param  string $dir  directory where the repo should be cloned
     * @param  string $name the name of the repo being cloned
     * @param \Closure $callback
     *
     * @return bool Success if the repo was suceesfully cloned.
     */
    public function cloneRepo($repo, $dir, $name, $callback = null)
    {
        $this->callback = $callback;

        if (!file_exists($dir) && !mkdir($dir, 0770, true)) {
            $this->sendMessage("Failed to create a directory at {$dir}");
            return false;
        }

        $builder = new ProcessBuilder(['/usr/bin/git']);

        // TODO: use unique user keys for SSH Auth.
        // $key = Auth::user()->auth_key;
        // $builder->setEnv("GIT_SSH_COMMAND", "ssh -i {$key}");
        if ($this->pub_key) {
            $ssh_cmd = "ssh -i {$this->pub_key} -o StrictHostKeyChecking=no";
            $builder->setEnv("GIT_SSH_COMMAND", $ssh_cmd);
            \Log::info("Setting GIT_SSH_COMMAND {$ssh_cmd}");
        } else {
            \Log::info("No public key found");
        }

        $builder->setTimeout(300)
                ->setWorkingDirectory($dir)
                ->add('clone')
                ->add('--progress')
                ->add($repo)
                ->add($name);

        $process = $builder->getProcess();
        $process->run(function($type, $buffer) {
            if (!empty($buffer)) {
                $this->sendMessage($buffer);
            }
        });
        return ($process->getExitCode() === 0);
    }

    private function sendMessage($buffer, $error = false, $firstLineOnly = true) {
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