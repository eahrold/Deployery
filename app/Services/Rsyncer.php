<?php

namespace App\Services;

use Symfony\Component\Process\Process;


class Rsyncer  {

    protected $name;
    protected $host;
    protected $username;
    protected $auth;
    protected $sshKey;
    protected $timeout;

    protected $dryRun = true;

    protected $exclusions = [];

    /**
     * Standard Out Callback
     * @var callable
     */
    protected $stdout = null;

    /**
     * Standard Error Callback
     * @var callable
     */
    protected $stderr = null;

    public function __construct($name, $host, $username, array $auth, $timeout=300)
    {

        if ($key = data_get($auth, 'key')) {
           $this->sshKey = $key;
        }

        $this->name = $name;
        $this->host = $host;
        $this->username = $username;
        $this->auth = $auth;
        $this->timeout = $timeout;

        $this->stdout = function($line) {
            logger("[RSYNC LOG] {$line}");
        };

        $this->stderr = function($line) {
            logger("[RSYNC ERROR] {$line}");
        };
    }

    public function setDryRun(bool $dryRun) {
        $this->dryRun = $dryRun;
        return $this;
    }

    public function getBaseArgs(string $from, string $to) {
        $$to = rtrim($to, '/');

        $args = [
            'rsync',
            '-av',
            '-e',
            "ssh -i {$this->sshKey}",
            rtrim($from, '/') . '/',
            "{$this->username}@{$this->host}:{$to}",
            '--exclude',
            '.git*',
        ];

        if ($this->dryRun) {
            $args[] = '--dry-run';
        }

        return array_merge($args, $this->exclusions);

    }

    public function excludeItem(string $item) {
        $this->exclusions[] = '--exclude';
        $this->exclusions[] = $item;
        return $this;
    }

    public function excludeItems(array $items) {
        foreach ($items as $item) {
            $this->excludeItem($item);
        }
        return $this;
    }

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

    protected function handleProcessOutput($type, $buffer, \Closure $stdout=null, \Closure $stderr=null) {
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
                if (empty($line)) return;

                if ($stdout) {
                    $stdout($line);
                }
                if (is_callable($this->stdout)) {
                    call_user_func($this->stdout, $line);
                }
            }
        }
    }

    public function rsync(string $from, string $to, \Closure $stdout=null, \Closure $stderr=null) {

        $args = $this->getBaseArgs($from, $to);

        $process = (new Process($args))
           ->setTimeout($this->timeout);

        $process->run(function ($type, $buffer) use ($stdout, $stderr) {
            $this->handleProcessOutput($type, $buffer, $stdout, $stderr);
        });

        return $process->getExitCode();
    }

}