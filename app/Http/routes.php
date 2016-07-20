<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//----------------------------------------------------------
// Api Routes
//-------------------------------------------------------
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['prefix' => 'projects'], function($api){
        $api->group(['prefix'=>'{project_id}/servers/{server_id}'], function($api){
            $api->post('/deploy', [
                'as'=>'api.projects.servers.deploy',
                'uses'=>'App\Http\Controllers\ServersController@deploy'
            ]);
        });
    });

    $api->get('webhooks/{webhook}',[
        'as'=>'api.projects.servers.webhooks',
        'uses'=>'App\Http\Controllers\ServersController@webhook'
    ]);
});

//----------------------------------------------------------
// CMS Routes
//-------------------------------------------------------
$router = app('Illuminate\Routing\Router');
$router->group(['middleware' => 'auth'], function($router){
    $router->resource('projects','ProjectsController');

    $router->group(['prefix'=>'projects/{projects}'], function($router){
        $router->group(['prefix'=>'servers'], function($router){
            $router->post('/{servers}/deploy', [
                'as'=>'projects.{projects}.servers.deploy',
                'uses'=>'ServersController@deploy'
            ]);
        });
        $router->resource('servers','ServersController', ['except'=>['index','show']]);
        $router->resource('config','ConfigController', ['except'=>['index','show']]);
    });
});


$router->get('/', function () {
    return view('welcome');
});

$router->auth();
$router->get('/home', 'ProjectsController@index');

