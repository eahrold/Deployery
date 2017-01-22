<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(["prefix" => "projects/{project}", "middleware" => "api.auth"], function ($api) {

        //----------------------------------------------------------
        // Project
        //-------------------------------------------------------
        $api->get('/info', [
            "as"=>"api.projects.info",
            "uses"=>"App\Http\Controllers\Api\ProjectsController@info"
        ]);

        $api->post('/clone-repo', [
            "as"=>"api.projects.clone-repo",
            "uses"=>"App\Http\Controllers\Api\ProjectsController@cloneRepo"
        ]);

        $api->get('/servers/pubkey', [
            "as"=>"api.servers.pubkey",
            "uses"=>"App\Http\Controllers\Api\ServersController@pubkey"
        ]);

        //----------------------------------------------------------
        // Servers
        //-------------------------------------------------------
        $api->group(["prefix"=>"/servers/{server}"], function ($api) {
            $api->post("/deploy", [
                "as"=>"api.projects.servers.deploy",
                "uses"=>"App\Http\Controllers\Api\ServersController@deploy"
            ]);

            $api->post("/test", [
                "as"=>"api.projects.servers.test",
                "uses"=>"App\Http\Controllers\Api\ServersController@test"
            ]);

            $api->get('/commit-details', [
                "as"=>"api.projects.servers.commit-details",
                "uses"=>"App\Http\Controllers\Api\ServersController@commit_details"
            ]);
        });
        $api->get('/servers/options', "App\Http\Controllers\Api\ServersController@options");
        $api->resource("/servers", "App\Http\Controllers\Api\ServersController", [
            "only"=>["show", "store", "update", "destroy" ],
        ]);

        //----------------------------------------------------------
        // Scripts
        //-------------------------------------------------------
        $api->get('/scripts/options', "App\Http\Controllers\Api\ScriptsController@options");
        $api->resource("/scripts", "App\Http\Controllers\Api\ScriptsController", [
            "only"=>[ "show", "store", "update", "destroy" ],
        ]);

        //----------------------------------------------------------
        // Configs
        //-------------------------------------------------------
        $api->get('/configs/options', "App\Http\Controllers\Api\ConfigsController@options");
        $api->resource("/configs", "App\Http\Controllers\Api\ConfigsController", [
            "only"=>[ "show", "store", "update", "destroy" ],
        ]);

        //----------------------------------------------------------
        // History
        //-------------------------------------------------------
        $api->resource("/history", "App\Http\Controllers\Api\HistoryController", [
            "only"=>[ "index", "show" ],
        ]);
    });

    ///----------------------------------------------------------
    // Project Resource
    //-------------------------------------------------------
    $api->group(["middleware" => "api.auth"], function ($api) {
        $api->get('/projects/options', "App\Http\Controllers\Api\ProjectsController@options");
        $api->resource("projects", "App\Http\Controllers\Api\ProjectsController");
    });

    //----------------------------------------------------------
    // Webhook
    //-------------------------------------------------------
    $api->group(["middleware" => "api.webhook"], function ($api) {
        $api->post("webhooks/{webhook}", [
            "as"=>"api.projects.servers.webhooks",
            "uses"=>"App\Http\Controllers\Api\ServersController@webhook"
        ]);
    });
});
