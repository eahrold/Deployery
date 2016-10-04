<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DeployeryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['Dingo\Api\Transformer\Factory']->setAdapter(function($app) {
            $fractal = new \League\Fractal\Manager;
            $fractal->setSerializer(new \App\Serializers\DataArraySerializer);
            return new \Dingo\Api\Transformer\Adapter\Fractal($fractal);
        });
    }
}
