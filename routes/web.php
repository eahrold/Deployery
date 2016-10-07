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


/**
 * Teamwork routes
 */
Route::group(['prefix' => 'teams', 'namespace' => 'Teamwork'], function()
{
    Route::get('/', 'TeamController@index')->name('teams.index');
    Route::get('create', 'TeamController@create')->name('teams.create');
    Route::post('teams', 'TeamController@store')->name('teams.store');
    Route::get('edit/{id}', 'TeamController@edit')->name('teams.edit');
    Route::put('edit/{id}', 'TeamController@update')->name('teams.update');
    Route::delete('destroy/{id}', 'TeamController@destroy')->name('teams.destroy');
    Route::get('switch/{id}', 'TeamController@switchTeam')->name('teams.switch');

    Route::get('members/{id}', 'TeamMemberController@show')->name('teams.members.show');
    Route::get('members/resend/{invite_id}', 'TeamMemberController@resendInvite')->name('teams.members.resend_invite');
    Route::post('members/{id}', 'TeamMemberController@invite')->name('teams.members.invite');
    Route::delete('members/{id}/{user_id}', 'TeamMemberController@destroy')->name('teams.members.destroy');

    Route::get('accept/{token}', 'AuthController@acceptInvite')->name('teams.accept_invite');
});
