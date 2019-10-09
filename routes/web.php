<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */

$router->group(['prefix' => 'courses'], function (Router $router) {
    $router->get('search', 'CourseController@search');
    $router->get('waterfall', 'CourseController@waterfall');
    $router->get('{code}', 'CourseController@show');

    $router->group(['prefix' => '{code}/comments'], function (Router $router) {
        $router->get('/', 'CommentController@index');
        $router->post('/', 'CommentController@store');
        $router->patch('{comments}/like', 'CommentController@like');
    });
});

$router->group(['prefix' => 'account'], function (Router $router) {
    $router->get('profile', 'AccountController@profile');
});

$router->group(['prefix' => 'auth'], function (Router $router) {
    $router->post('sign-in', 'AuthController@signIn');
    $router->post('sign-up', 'AuthController@signUp');
    $router->post('sign-out', 'AuthController@signOut');
});
