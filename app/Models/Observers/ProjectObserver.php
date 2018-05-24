<?php

namespace App\Models\Observers;

use App\Jobs\RepositoryClone;
use App\Models\User;
use App\Services\Git\GitCloner;
use App\Services\SSHKeyer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ProjectObserver {
    public function updating($model) {
        if( $model->repo_exists && $model->isDirty('repo')) {
            (new GitCloner)->updateRepoUrl($model->repo_path, $model->repo);
        }
    }

    public function saved($model) {
        if (!$model->repo_exists) {
            dispatch((new RepositoryClone($model))->onQueue('clones'));
        }
    }

    public function saving($model) {
        $model->slug = str_slug($model->slug ?: $model->name);
    }

    // Register for events
    public function creating($model) {
        $user = Auth::user();
        $model->uid = uniqid();
        $model->user_id = $user->id;
        $model->team_id = ($user && $user instanceof User) ? $user->current_team_id : -1;
    }

    public function created($model) {
        $keyer = new SSHKeyer();
        $keyer->generate($model->keyPath(), true);
    }

    public function deleting($model) {
        if ($model->uid) {
            File::deleteDirectory($model->fileStore());
        }
        // Clears any cached keys
        $model->is_cloning = false;
        $model->is_deploying = false;
    }

}