<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */

$router->group(['prefix' => 'courses'], function (Router $router) {
    $router->get('search', 'CourseController@search');
    $router->get('waterfall', 'CourseController@waterfall');
    $router->get('{courses}', 'CourseController@show');

    $router->group(['prefix' => '{courses}/comments'], function (Router $router) {
        $router->get('/', 'CommentControl@index');
        $router->post('/', 'CommentControl@store');
        $router->patch('{comments}/like', 'CommentControl@like');
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
