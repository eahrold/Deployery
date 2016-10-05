<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;


class GitProcessManager
{

    //----------------------------------------------------------
    // Settings
    //-------------------------------------------------------

    /**
     * Currenty working directory
     * @var string
     */
    private $cwd = '';

    /**
     * Envrionmental variables
     * @var array
     */
    private $env = [];

    /**
     * Set the current working directory
     *
     * @param string $cwd current working directory
     *
     * @return  this chainable
     */
    public function setWorkingDirectory($cwd) {
        $this->cwd = $cwd;
        return $this;
    }

    /**
     * Sets environment variables.
     *
     * @return  this chainable
     */
    public function setEnv(array $env)
    {
        $this->env = $env;
        return $this;
    }

    private $pub_key;
    public function withPubKey($pub_key)
    {
        $this->pub_key = $pub_key;
        return $this;
    }

    private $password;
    public function withPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * An initialized builder for git processes
     *
     * @return ProcessBuilder builder
     */
    protected function newBuilder()
    {
        $builder = new ProcessBuilder(['/usr/bin/git']);
        $builder->setWorkingDirectory($this->cwd);
        if ($this->pub_key) {
            $ssh_cmd = "ssh -i {$this->pub_key} -o StrictHostKeyChecking=no";
            $builder->setEnv("GIT_SSH_COMMAND",  $ssh_cmd);
        }
        return $builder;
    }


    //----------------------------------------------------------
    // Pipes
    //-------------------------------------------------------

    /**
     * Standard Out Callback
     * @var callable
     */
    private $stdout = null;

    /**
     * Standard Error Callback
     * @var callable
     */
    private $stderr = null;

    /**
     * Set the standard out pipe
     *
     * @param Callable the function callback for std_err
     * @return  ProcessManager current process manager object
     */
    public function setStdOut($fn) {
        if (is_callable($fn)) {
            $this->stdout = $fn;
        }
        return $this;
    }

    public function setStdErr($fn) {
    /**
     * Set the standard error pipe
     *
     * @param Callable the function callback for std_err
     * @return  ProcessManager current process manager object
     */
        if (is_callable($fn)) {
            $this->stderr = $fn;
        }
        return $this;
    }


    //----------------------------------------------------------
    // Proc
    //-------------------------------------------------------


    /**
     * Execute a script like process
     *
     * @param  string $script  task to execute
     * @param  mixed  $stdout  std_out callback
     * @param  mixed  $stderr  std_err callback
     * @return int            process exit code
     */
    private function exec($script, $stdout = null, $stderr = null) {
        $builder = $this->newBuilder();
        $args = explode(' ', $script);
        foreach ($args as $arg) {
            $builder->add($arg);
        }
        $process = $builder->getProcess();


        $process->run(function($type, $buffer) use ($stdout, $stderr){
            if (Process::ERR === $type) {
                if ($stderr) {
                    $stderr($buffer);
                }
                if (is_callable($this->stderr)) {
                    call_user_func($this->stderr, $buffer);
                }
            } else {
                $lines = explode(PHP_EOL, $buffer);
                foreach ($lines as $line) {
                    if ($stdout) {
                        $stdout($line);
                    }
                    if (is_callable($this->stdout)) {
                        call_user_func($this->stdout, $line);
                    }
                }
            }
        });
        return $process->getExitCode();
    }

    /**
     * Run a group of tasks
     *
     * @param  mixed   $tasks  A taks or array of tasks to execute
     * @param  mixed $stdout   std_out callback
     * @param  mixed $stderr   std_err callback
     * @param  boolean $force  Carry on executing task list regardless of exit code of previous taks
     * @return int    last process exit code,
     */
    public function run($tasks, $stdout = null, $stderr = null, $force = false) {
        if (!is_array($tasks)) {
            $tasks = [$tasks];
        }
        $rc = 0;
        foreach ($tasks as $task) {
            $rc = $this->exec($task, $stdout, $stderr);
            if ($rc !== 0) {
                if (!$force) {
                    break;
                }
            }
        }
        return $rc;
    }
}