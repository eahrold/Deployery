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

///----------------------------------------------------------
// Project Resource
//-------------------------------------------------------
\Route::group(["middleware" => "auth:api", 'as' => 'api.'], function ($api) {

    $api->group(["prefix" => "projects", 'as' => 'projects.'], function ($api) {
        $api->get('options', [
            "uses" => "Api\ProjectsController@options",
            "as" => 'options',
        ]);

        $api->get('branch-commits', [
            'as' => 'branch.commits',
            'uses' => "Api\DeploymentController@getBranchCommits"
        ]);

        $api->group(['prefix' => '{project}'], function($api){
            //----------------------------------------------------------
            // Project
            //-------------------------------------------------------
            $api->get('/info', [
                "as"=>"info",
                "uses"=>"Api\ProjectsController@info"
            ]);

            $api->post('/clone-repo', [
                "as"=>"clone-repo",
                "uses"=>"Api\ProjectsController@cloneRepo"
            ]);

            $api->get('/servers/pubkey', [
                "as"=>"servers.pubkey",
                "uses"=>"Api\ServersController@pubkey"
            ]);

            //----------------------------------------------------------
            // Servers
            //-------------------------------------------------------
            $api->group(["prefix"=>"/servers/{server}"], function ($api) {
                $api->post("/test", [
                    "as"=>"servers.test",
                    "uses"=>"Api\ServersController@test"
                ]);

                $api->post("/deploy", [
                    "as"=>"servers.deploy",
                    "uses"=>"Api\DeploymentController@deploy"
                ]);

                $api->get('/commit-details', [
                    "as"=>"servers.commit-details",
                    "uses"=>"Api\DeploymentController@commitDetails"
                ]);

                $api->get('/find-commit', [
                    "as"=>"servers.find-commit",
                    "uses"=>"Api\DeploymentController@findCommit"
                ]);

                $api->put("/webhook/reset", [
                    "as"=>"servers.webhook.reset",
                    "uses"=>"Api\ServersController@webhookReset"
                ]);
            });

            $api->get('servers/options', [
                'uses' => "Api\ServersController@options",
                'as' => 'servers',
            ]);

            $api->resource("servers", "Api\ServersController", [
                "only"=>["show", "store", "update", "destroy" ],
            ]);

            //----------------------------------------------------------
            // Scripts
            //-------------------------------------------------------
            $api->get('/scripts/options', [
                'uses' => "Api\ScriptsController@options",
                'as' => 'scripts.options'
            ]);
            $api->resource("/scripts", "Api\ScriptsController", [
                "only"=>[ "show", "store", "update", "destroy" ],
            ]);

            //----------------------------------------------------------
            // Configs
            //-------------------------------------------------------
            $api->get('configs/options', [
                'uses' => "Api\ConfigsController@options",
                'as' => 'configs.options',
            ]);

            $api->resource("configs", "Api\ConfigsController", [
                "only" => [ "show", "store", "update", "destroy" ],
            ]);

            //----------------------------------------------------------
            // History
            //-------------------------------------------------------
            $api->resource("/history", "Api\HistoryController", [
                "only"=>[ "index", "show" ],
            ]);
        });
    });
    $api->resource("projects", "Api\ProjectsController");

    $api->resource('my-account', "Api\MyAccountController", [
        'only' => ['show', 'update', 'destroy']
    ]);

});

//----------------------------------------------------------
// Webhook
//-------------------------------------------------------
\Route::group(["middleware" => "api.webhook"], function ($api) {
    $api->post("webhooks/{webhook}", [
        "as"=>"api.projects.servers.webhooks",
        "uses"=>"Api\DeploymentController@webhook"
    ]);
});

