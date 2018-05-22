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
     * String Seperator unlikely to occur anywhere in a commit.
     * Used for explosion
     */
    const PRETTY_FORMAT_DELIM = '|-*-*-|';

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
     * Convert a stdout line to a commit array
     * @param  string $line the stdout text for a commit
     * @return array       array representation fo a commit
     */
    private function formatCommitLine($line)
    {
        list($hash, $user, $date, $message) = explode(static::PRETTY_FORMAT_DELIM, $line);
        return [
            'hash' => trim($hash),
            'user' => trim($user),
            'date' => trim($date),
            'message' => trim($message)
        ];
    }

    /**
     * Create a common format for git log results
     * @return string git log format
     */
    private function gitLogFormat()
    {
        return "--pretty=format:%h".static::PRETTY_FORMAT_DELIM."%an".static::PRETTY_FORMAT_DELIM."%aI".static::PRETTY_FORMAT_DELIM."%s";
    }

    /**
     * Get a list of commits for a given branhc
     *
     * @param  integer $take Number of commits to display
     * @return array         List of commits
     */
    public function commits($take = 10)
    {
        $task = "log origin/{$this->branch} --no-merges -n{$take} {$this->gitLogFormat()}";
        $builder = $this->gitBuilder()->setTask($task);

        $stdout = $this->run($builder);

        $commits = [];
        foreach ($stdout as $line) {
            $commits[] = $this->formatCommitLine($line);
        }
        return $commits;
    }


    /**
     * Get the first commit to the repo.
     *
     * @return array first commit to the repo.
     */
    public function getInitialCommitProperty()
    {
        $task = "rev-list {$this->gitLogFormat()} --max-parents=0 HEAD";
        $builder = $this->gitBuilder()->setTask($task);
        $stdout = $this->run($builder);
        if(count($stdout)) {
            return $this->formatCommitLine(end($stdout));
        }
    }

    /**
     * Get the most recent commit.
     *
     * @return array The newest commit
     */
    public function getNewestCommitProperty()
    {
        $task = "log origin/{$this->branch} -1 {$this->gitLogFormat()}";
        $builder = $this->gitBuilder()->setTask($task);
        $stdout = $this->run($builder);

        return $this->formatCommitLine(end($stdout));
    }

    /**
     * Get the most recent commit.
     *
     * @return array The newest commit
     */
    public function findCommitProperty($commitHash)
    {
        // Clean Hash //
        $commitHash = substr(preg_replace("/[^A-Za-z0-9 ]/", '', $commitHash),0, 7);

        $task = "log --all --oneline {$commitHash} {$this->gitLogFormat()}";
        $builder = $this->gitBuilder()->setTask($task);
        $stdout = $this->run($builder);

        $end = collect($stdout)->filter(function($item) use ($commitHash){
            return starts_with($item, $commitHash);
        })->first();

        if (empty($end)) return false;
        return $this->formatCommitLine($end);
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
        // The -z arg here instructs git to use NULs as output field terminators
        // Consequently the result will be a single line
        $diff_filters='AMDR'; // (A)dd, (M)Modified, (D)elete, (R)ename
        $builder = $this->gitBuilder()->setTask("diff --name-status -z --diff-filter={$diff_filters} {$from}");
        if ($to) {
            $builder->add($to);
        }

        $stdout = data_get($this->run($builder), 0);

        // First we'll normalize RXXX (rename) values to just R
        // From https://git-scm.com/docs/git-status
        //     <X><score>  The rename or copy score (denoting the percentage
        //     of similarity between the source and target of the
        //     move or copy). For example "R100" or "C75".
        $stdout = preg_replace('/([R])\d+\0/', "R\0", $stdout);

        // Now we'll set up things so we can get a clean explosion
        $df_split = str_split($diff_filters);
        foreach ($df_split as $key) {
            // The \0 before represents the end of the previous line, we replace this
            // with the PRETTY_FORMAT_DELIM, to differentiate it from the
            // rest of the NUL field terminators.
            // The $key is the first part of the new line (the action),
            // The following \0 is between the action and the file
            $stdout = str_replace("\0{$key}\0", static::PRETTY_FORMAT_DELIM."{$key}\0", $stdout);
        }
        $files = array_filter(explode(static::PRETTY_FORMAT_DELIM, $stdout));

        $changed = [];
        $removed = [];

        foreach ($files as $result) {
            // $parts = preg_split('/\s+/', $result);
            $parts = explode("\0", $result);
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
