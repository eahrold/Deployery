<?php

namespace App\Services\Git;

use App\Exceptions\Git\GitException;
use App\Exceptions\Git\GitInvalidBranchException;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Get Git Info for a repo and branch.
 */
class GitProcessBuilder extends ProcessBuilder
{

    public function __construct(string $repo, $branch = 'master')
    {
        parent::__construct(['/usr/bin/git']);
        $this->setWorkingDirectory($repo);
    }

    /**
     * Update the ProcessBuilder to use a specific public key.
     * @param  string|null $pub_key [description]
     * @return [type]               [description]
     */
    public function withPubKey(string $pub_key=null)
    {
        if ($pub_key) {
            $ssh_cmd = "ssh -i {$pub_key} -o StrictHostKeyChecking=no";
            $this->setEnv("GIT_SSH_COMMAND", $ssh_cmd);
        }
        return $this;
    }

    /**
     *
     */
    private $password;
    public function withPassword(string $password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Update the ProcessBuilder's args using a string.
     *
     * @param string $task [description]
     */
    public function setTask(string $task)
    {
        $args = explode(' ', $task);
        foreach ($args as $arg) {
            $this->add($arg);
        }
        return $this;
    }
}
