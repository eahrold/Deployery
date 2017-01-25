<?php

namespace App\Models\Traits;

use App\Services\Git\GitInfo;
use App\Services\SSHConnection;

trait GitInfoTrait {

    /**
     * Underlying GitInfo instance
     * @var \App\Services\Git\GitInfo
     */
    private $gitInfoInstance;

    /**
     * Getter for the GitInfo instance
     *
     * @return \App\Services\Git\GitInfo
     */
    public function getGitInfoAttribute()
    {
        if (!isset($this->gitInfoInstance)) {
            if ($branch = $this->branch ?: Input::get('branch') ?: $this->project->branch) {
                $this->gitInfoInstance = (new GitInfo($this->repo, $branch))->withPubKey($this->project->user->auth_key);
            }
        }
        return $this->gitInfoInstance;
    }

    /**
     * Update the gitinfo repo instance
     *
     * @return $this
     */
    public function updateGitInfo() {
        $this->git_info->update();
        return $this;
    }

    /**
     * Getter for the git commits attribute
     *
     * @return array of git commits
     */
    public function getCommitsAttribute()
    {
        return $this->git_info->commits(10);
    }

    /**
     * Getter for the newest_commit attribute
     *
     * @return associative array
     */
    public function getNewestCommitAttribute($value = '')
    {
        return $this->git_info->newest_commit;
    }

    /**
     * Getter for the initial_commit attribute
     *
     * @return associative array
     */
    public function getInitialCommitAttribute($value = '')
    {
        return $this->git_info->initial_commit;
    }

    //----------------------------------------------------------
    // Repo
    //-------------------------------------------------------
    public function getRepoAttribute($value = '')
    {
        if ($this->project) {
            return $this->project->repo_path;
        }
    }
}
