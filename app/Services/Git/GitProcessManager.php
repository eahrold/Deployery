<?php

namespace App\Services\Git;

use App\Services\Git\Traits\GitAuthenticatable;
use Symfony\Component\Process\Process;

class GitProcessManager
{
    use GitAuthenticatable;

    /**
     * Currenty working directory
     * @var string
     */
    private $repo = '';

    /**
     * Envrionmental variables
     * @var array
     */
    private $env = [];

    /**
     * Initialize
     * @param string $repo path to repo
     */
    public function __construct(string $repo)
    {
        $this->repo = $repo;
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

    /**
     * An initialized builder for git processes
     *
     * @return ProcessBuilder builder
     */
    protected function newBuilder()
    {
        $builder = new GitProcessBuilder($this->repo);
        if ($this->pub_key) {
            $builder->withPubKey($this->pub_key);
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
    public function setStdOut($fn)
    {
        if (is_callable($fn)) {
            $this->stdout = $fn;
        }
        return $this;
    }

    /**
     * Set the standard error pipe
     *
     * @param Callable the function callback for std_err
     * @return  ProcessManager current process manager object
     */
    public function setStdErr($fn)
    {
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
     * @param  string $task    task to execute
     * @param  mixed  $stdout  std_out callback
     * @param  mixed  $stderr  std_err callback
     * @return int            process exit code
     */
    private function exec($task, $stdout = null, $stderr = null)
    {
        $builder = $this->newBuilder()->setTask($task);
        $process = $builder->getProcess();

        $process->run(function ($type, $buffer) use ($stdout, $stderr) {
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
     * @param  mixed   $tasks   A taks or array of tasks to execute
     * @param  mixed   $stdout  std_out callback
     * @param  mixed   $stderr  std_err callback
     * @param  boolean $force   Continue executing task list regardless of exit code of previous taks
     * @return int              last process exit code,
     */
    public function run($tasks, $stdout = null, $stderr = null, $force = false)
    {
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
