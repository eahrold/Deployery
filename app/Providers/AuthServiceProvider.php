<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Policies\ProjectPolicy;
use App\Policies\TeamPolicy;
use App\Policies\UserPolicy;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Team::class => TeamPolicy::class,
        Project::class => ProjectPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);
    }
}
