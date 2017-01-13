<?php

namespace App\Services\Git;

use App\Services\Git\Exceptions\GitException;
use App\Services\Git\Exceptions\GitInvalidBranchException;
use App\Services\Git\GitProcessManager;
use App\Services\Git\Traits\GitAuthenticatable;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

/**
 * Get Git Info for a repo and branch.
 *
 * @property newest_commit
 * @property initial_commit
 *
 */
class GitInfo
{
    use GitAuthenticatable;

    /**
     * Path to the repo
     *
     * @var string
     */
    public $repo;

    /**
     * Name of the branch
     *
     * @var string
     */
    protected $branch;

    public function __construct($repo, $branch = 'master')
    {
        $this->repo = $repo;
        $this->branch = $branch;
    }

    /**
     * An initialized builder for git processes
     *
     * @return ProcessBuilder builder
     */
    protected function gitBuilder()
    {
        return new GitProcessBuilder($this->repo, $this->branch);
    }

    /**
     * Run the git process
     *
     * @param  GitProcessBuilder $builder fully constructed builder
     * @return array                      array of lines from buffered output.
     */
    protected function run(GitProcessBuilder $builder)
    {
        $output = "";
        $process = $builder->getProcess();
        $process->run(function ($type, $buffer) use (&$output) {
            if (Process::ERR !== $type) {
                if (!empty($buffer)) {
                    $output .= $buffer;
                }
            }
        });
        $results = explode(PHP_EOL, $output);

        $results = array_filter($results, function ($item) {
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
    public function branch(string $branch)
    {
        $this->validateBranch($branch);
        $this->branch = $branch;
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
        $task = "log origin/{$this->branch} --oneline --no-merges -n{$take}";
        $builder = $this->gitBuilder()->setTask($task);

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

    /**
     * Get the most recent commit.
     *
     * @return array The newest commit
     */
    public function getNewestCommitProperty()
    {
        $task = "log origin/{$this->branch} -1 --oneline";
        $builder = $this->gitBuilder()->setTask($task);
        $stdout = $this->run($builder);
        list($hash, $message) = explode(' ', end($stdout), 2);
        return ['hash' => trim($hash), 'message' => trim($message)];
    }

    /**
     * Get the first commit to the repo.
     *
     * @return array first commit to the repo.
     */
    public function getInitialCommitProperty()
    {
        $task = "rev-list --oneline --max-parents=0 HEAD";
        $builder = $this->gitBuilder()->setTask($task);
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
        $builder = $this->gitBuilder()->setTask('branch -a');

        $branches = $this->run($builder);
        $branches = collect($branches);

        return $branches->reject(function ($item) {
            return str_contains($item, 'HEAD') || empty($item);
        })
        ->transform(function ($item) {
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
        $builder = $this->gitBuilder()->setTask('fetch --all');
        $this->run($builder);
        return $this;
    }

    /**
     * Do a heavy handed repo update
     *
     * @return  void
     */
    public function update()
    {
        $tasks = [
            "fetch origin",
            "reset --hard origin/{$this->branch}",
            "pull"
        ];

        $manager = new GitProcessManager($this->repo);
        $manager->withPubKey($this->pub_key)
                ->run($tasks, null, null, true);

        return $this;
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
        $builder = $this->gitBuilder()->setTask("diff --name-status {$from}");
        if ($to) {
            $builder->add($to);
        }

        $stdout = $this->run($builder);
        $files = [];
        foreach ($stdout as $buffered) {
            $lines = explode(PHP_EOL, $buffered);
            $files = array_merge($files, $lines);
        }
        $changed = [];
        $removed = [];

        foreach ($files as $result) {
            $parts = preg_split('/\s+/', $result);
            $char = trim($parts[0])[0];
            $file = trim($parts[1]);

            switch ($char) {
                case 'A':
                case 'M':
                    $changed[] = $file;
                    break;
                case 'D':
                    $removed[] = $file;
                    break;
                case 'R':
                    // Renamed files are formatted like this...
                    // R087    app/Models/FromSomeFile.php  app/Services/ToSomeFile.php
                    $removed[] = $file;
                    $changed[] = trim($parts[2]);
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
        $builder = $this->gitBuilder()->add('ls-files');
        $stdout = $this->run($builder);
        $changed = [];
        foreach ($stdout as $buffered) {
            $lines = explode(PHP_EOL, $buffered);
            $changed = array_merge($changed, $lines);
        }
        $removed = [];
        return compact('changed', 'removed');
    }

    public function size()
    {
        $builder = $this->gitBuilder()
                        ->add('count-objects')
                        ->add('-v')
                        ->add('-H');

        $stdout = $this->run($builder);
        $size_pack = array_filter($stdout, function($line){
            return Str::startsWith($line, 'size-pack');
        });

        list(/*'size-pack'*/, $size) = explode(':', end($size_pack));
        return trim($size);
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
