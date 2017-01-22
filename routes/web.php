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

    $router->group(["prefix"=>"projects/{project}"], function ($router) {
        $router->get('', [
            'uses' => 'ProjectsController@show',
            'as' => 'projects.edit'
        ]);
    });

    $router->group(["middleware" => "admin"], function ($router) {
        $router->resource("users", "UsersController", [ "except"=>[ "show" ]]);
    });
});

$router->auth();


/**
 * Teamwork routes
 */
$router->group(['prefix' => 'teams', 'namespace' => 'Teamwork'], function($router)
{
    $router->get('/', 'TeamController@index')->name('teams.index');
    $router->get('create', 'TeamController@create')->name('teams.create');
    $router->post('teams', 'TeamController@store')->name('teams.store');
    $router->get('edit/{id}', 'TeamController@edit')->name('teams.edit');
    $router->put('edit/{id}', 'TeamController@update')->name('teams.update');
    $router->delete('destroy/{id}', 'TeamController@destroy')->name('teams.destroy');

    $router->get('switch/{id}', 'TeamController@switchTeam')->name('teams.switch');
    $router->get('switch/{id}/{type}', 'TeamController@switchTeam')->name('teams.switch.alt');


    $router->get('members/{id}', 'TeamMemberController@show')->name('teams.members.show');
    $router->get('members/resend/{invite_id}', 'TeamMemberController@resendInvite')->name('teams.members.resend_invite');
    $router->post('members/{id}', 'TeamMemberController@invite')->name('teams.members.invite');

    $router->delete('members/leave/{id}', 'TeamMemberController@leave')->name('teams.members.leave');
    $router->post('members/join/{id}', 'TeamMemberController@join')->name('teams.members.join');

    $router->delete('members/{id}/{user_id}', 'TeamMemberController@destroy')->name('teams.members.destroy');

    $router->get('accept/{token}', 'AuthController@acceptInvite')->name('teams.accept_invite');
});
