<?php

namespace App\Services\Git;

use App\Services\Git\Traits\GitAuthenticatable;

/**
 * Get Git Info for a repo and branch.
 */
class GitPreparer
{
    use GitAuthenticatable;

    /**
     * Prepare the git repo for deployment
     *
     * @param  string   $repo     Path to the repo
     * @param  string   $branch   branch to use
     * @param  string   $to       the commit the repo should be set to
     * @param  \Closure $callback Progress message.
     * @return void
     */
    public function prepare(string $repo, string $branch, string $to, \Closure $callback = null)
    {
        $tasks = [
            "fetch origin",
            "reset --hard origin/{$branch}",
            "pull",
            "reset --hard {$to}",
            "clean -xdf",
        ];

        $manager = new GitProcessManager($repo);
        $manager->withPubKey($this->pub_key)
                ->run($tasks, null, $callback, true);
    }
}
