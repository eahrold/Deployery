<?php

namespace App\Providers;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Queue;
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
        Queue::failing(function (JobFailed $event) {
            // $event->connectionName
            // $event->job
            // $event->exception
            \Log::error("Job Failed {$event->exception->getMessage()}");
        });
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
