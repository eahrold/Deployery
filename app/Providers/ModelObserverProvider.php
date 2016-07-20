<?php

namespace App\Providers;

use App\Models\Observers\ProjectObserver;
use App\Models\Project;
use Illuminate\Support\ServiceProvider;

class ModelObserverProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Project::observe(new ProjectObserver());
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
