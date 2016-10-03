<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Validator::resolver( function( $translator, $data, $rules, $messages ) {
            return new \App\Validators\GitValidator( $translator, $data, $rules, $messages );
        });

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
