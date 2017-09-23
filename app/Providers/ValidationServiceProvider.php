<?php

namespace App\Providers;

use App\Services\Git\Validation\GitBranchValidation;
use Illuminate\Support\ServiceProvider;
use Validator;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('git_branch', GitBranchValidation::class . '@validate');
        Validator::replacer('git_branch', GitBranchValidation::class . '@replace');
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
