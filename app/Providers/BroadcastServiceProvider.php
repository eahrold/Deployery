<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

        /*
         * Authenticate the user's personal channel...
         */
        Broadcast::channel('App.User.*', function($user, $userId) {
            return (int)$user->id === (int)$userId;
        });

        /*
         * Authenticate the Project channel
         */
        Broadcast::channel('project.*', function($user, $projectId) {
            if ($project = \Project::find($projectId)) {
                return $user->isTeamMember($project->team);
            }
            return false;
        });

        /*
         * Authenticate to the presence channel;
         */
        Broadcast::channel('project-viewers.*', function($user, $projectId) {
            return ['id'=>$user->id, 'email'=>$user->email, 'name' => $user->full_name];
        });
    }
}
