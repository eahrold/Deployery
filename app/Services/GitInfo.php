<?php

namespace App\Services;

use App\Exceptions\Git\GitException;
use App\Exceptions\Git\GitInvalidBranchException;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Get Git Info for a repo and branch.
 */
class GitInfo
{
    public $_repo;
    protected $_branch;

    private $pub_key;
    private $password;

    public function __construct($repo, $branch = 'master')
    {
        $this->_repo = $repo;
        $this->_branch = $branch;
        //$this->fetch();
    }

    public function withPubKey($pub_key)
    {
        $this->pub_key = $pub_key;
        return $this;
    }

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
    protected function builder()
    {
        $builder = new ProcessBuilder(['/usr/bin/git']);
        $builder->setWorkingDirectory($this->_repo);
        if ($this->pub_key) {
            $ssh_cmd = "ssh -i {$this->pub_key} -o StrictHostKeyChecking=no";
            $builder->setEnv("GIT_SSH_COMMAND",  $ssh_cmd);
        }
        return $builder;
    }

    /**
     * Run the git process
     *
     * @param  ProcessBuilder $builder fully constructed builder
     * @return array                   array of lines from buffered output.
     */
    protected function run(ProcessBuilder $builder)
    {
        $output = "";
        $process = $builder->getProcess();
        $process->run(function($type, $buffer) use (&$output) {
            if (Process::ERR !== $type) {
                if (!empty($buffer)) {
                    $output .= $buffer;
                }
            }
        });
        $results = explode(PHP_EOL, $output);

        $results = array_filter($results, function($item) {
            return !empty($item);
        });
        return $results;
    }

    /**
     * Change the branch for GitInfo process
     *
     * @param  string $branch the branch to switch to.
     * @return $this          this
     */
    public function branch($branch)
    {
        $this->validateBranch($branch);
        $this->_branch = $branch;
        return $this;
    }

    /**
     * Get a list of commits for a given branhc
     *
     * @param  integer $take Number of commits to display
     * @return array         List of commits
     */
    public function commits($take = 10)
    {
        $builder = $this->builder()->add('log')
                                    ->add("origin/{$this->_branch}")
                                    ->add('--oneline')
                                    ->add('--no-merges')
                                    ->add("-n{$take}");
        $stdout = $this->run($builder);
        $commits = [];
        foreach ($stdout as $line) {
            list($hash, $message) = explode(' ', $line, 2);
            $commits[] = [
                'hash' => trim($hash),
                'message' => trim($message)
            ];
        }
        return $commits;
    }

    public function getNewestCommitProperty()
    {
        $commits = $this->commits();
        if (count($commits)) {
            return $commits[0];
        }
    }

    public function getInitialCommitProperty()
    {
        $builder = $this->builder()->add('rev-list')
                                    ->add('--oneline')
                                    ->add('--max-parents=0')
                                    ->add('HEAD');
        $stdout = $this->run($builder);

        list($hash, $message) = explode(' ', $stdout[0], 2);
        return compact('hash', 'message');
    }

    /**
     * Get the branches for the model
     *
     * @return array Available branches for the model
     */
    public function branches()
    {
        $builder = $this->builder()->add('branch')
                                    ->add('-a');

        $branches = $this->run($builder);
        $branches = collect($branches);

        return $branches->reject(function($item) {
            return str_contains($item, 'HEAD') || empty($item);
        })
        ->transform(function($item) {
            $item = trim(str_replace("*", "", $item));
            return str_replace('remotes/origin/', "", $item);
        })
        ->unique()->values()->all();
    }

    /**
     * Check if particular branch exists in the repo
     *
     * @param  string $branch branch name
     * @return boolean         true if branch exists, false otherwise.
     */
    public function hasBranch($branch)
    {
        return in_array($branch, $this->branches());
    }

    /**
     * Validates whether a particular branch exists for the repo.
     *
     * @param  string $branch branch name
     * @return  void
     */
    private function validateBranch($branch)
    {
        if (!$this->hasBranch($branch)) {
            throw new GitInvalidBranchException("{$branch} is not a valid branch");
        }
    }

    /**
     * Fetch info from remote repo
     *
     * @return  void
     */
    public function fetch()
    {
        $builder = $this->builder()->add('fetch')
                                    ->add('--all');
        $this->run($builder);
    }
    /**
     * Show Changes between two commits
     *
     * @param  string $from From commit
     * @param  string $to   To commit
     * @return array        Associative array of changed files with two keys "Changed" and "Removed"
     */
    public function changes($from, $to = null)
    {
        $builder = $this->builder()->add('diff')
                                    ->add('--name-status')
                                    ->add($from);
        if ($to) {
            $builder->add($to);
        }
        $data = $this->run($builder);
        $files = [];
        foreach ($data as $buffered) {
            $lines = explode(PHP_EOL, $buffered);
            $files = array_merge($files, $lines);
        }
        $changed = [];
        $removed = [];

        foreach ($files as $result) {
            $char = substr($result, 0, 1);
            $file = substr($result, 1);
            switch ($char) {
                case 'A':
                case 'M':
                    $changed[] = trim($file);
                    break;
                case 'D':
                    $removed[] = trim($file);
                    break;
                default:
                    break;
            }
        }
        return compact('changed', 'removed');
    }

    /**
     * Get all the files under source control
     *
     * @return array Array of changed and removed files
     */
    public function all()
    {
        $builder = $this->builder()->add('ls-files');
        $data = $this->run($builder);
        $changed = [];
        foreach ($data as $buffered) {
            $lines = explode(PHP_EOL, $buffered);
            $changed = array_merge($changed, $lines);
        }
        $removed = [];
        return compact('changed', 'removed');
    }

    /**
     * Get props dynamically
     *
     * @param  string $key property key
     * @return mixed       associated property
     */
    public function __get($key)
    {
        return $this->getDynamicProperty($key);
    }

    /**
     * Determine if a get mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasDynamicPropertyMutator($key)
    {
        return method_exists($this, 'get'.Str::studly($key).'Property');
    }

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getDynamicProperty($key)
    {
        if ($this->hasDynamicPropertyMutator($key)) {
            return $this->{'get'.Str::studly($key).'Property'}();
        }
        throw new GitException("Dynamic Property {$key} doesn't exist on {$this->getClassName()}");
    }

    public function getClassName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}
