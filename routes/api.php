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
    $api->group(["prefix" => "projects", "middleware" => "api.auth"], function ($api) {

        $api->group(["prefix"=>"{project}/servers/{server}"], function ($api) {
            $api->post("/deploy", [
                "as"=>"api.projects.servers.deploy",
                "uses"=>"App\Http\Controllers\Api\ServersController@deploy"
            ]);

            $api->post("/test", [
                "as"=>"api.projects.servers.test",
                "uses"=>"App\Http\Controllers\Api\ServersController@test"
            ]);

            $api->get('commit-details', [
                "as"=>"api.projects.servers.commit-details",
                "uses"=>"App\Http\Controllers\Api\ServersController@commit_details"
            ]);
        });

        $api->post('{project}/clone-repo', [
            "as"=>"api.projects.clone-repo",
            "uses"=>"App\Http\Controllers\Api\ProjectsController@cloneRepo"
        ]);

        $api->resource("/", "App\Http\Controllers\Api\ProjectsController", [
            "only"=>["store","update","destroy"],
        ]);

        $api->resource("{project}/scripts", "App\Http\Controllers\Api\ScriptsController", [
            "only"=>["store","update","destroy"],
        ]);

        $api->resource("{project}/configs", "App\Http\Controllers\Api\ConfigsController", [
            "only"=>["store","update","destroy"],
        ]);
    });

    $api->group(["middleware" => "api.webhook"], function ($api) {
        $api->get("webhooks/{webhook}", [
            "as"=>"api.projects.servers.webhooks",
            "uses"=>"App\Http\Controllers\Api\ServersController@webhook"
        ]);
    });
});
