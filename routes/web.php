<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

$router = app("Illuminate\Routing\Router");

$router->group(["middleware" => "auth"], function ($router) {
    $router->get("/", "ProjectsController@index");
    $router->resource("projects", "ProjectsController");

    $router->group(["prefix"=>"projects/{project}"], function ($router) {
        $router->group(["prefix"=>"servers"], function ($router) {
            $router->get("/{servers}/deploy", [
                "as"=>"servers.deploy",
                "uses"=>"ServersController@deploy"
            ]);
        });
        $router->resource("servers", "ServersController", ["except"=>["index","show"]]);
        $router->resource("configs", "ConfigController", ["except"=>["index","show"]]);
        $router->resource("scripts", "ScriptsController", ["except"=>["index","show"]]);
    });

    $router->group(["middleware" => "admin"], function ($router) {
        $router->resource("users", "UsersController", ["except"=>["show"]]);
    });
});

$router->auth();
