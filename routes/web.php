<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */

$router->group(['prefix' => 'courses'], function (Router $router) {
    $router->get('search', 'CourseController@search');

    $router->get('comments', 'CommentController@latest');

    $router->get('{code}', 'CourseController@show');

    $router->group(['prefix' => '{code}/comments'], function (Router $router) {
        $router->get('/', 'CommentController@index');

        $router->post('/', [
            'middleware' => ['auth'],
            'uses' => 'CommentController@store',
        ]);
    });
});

$router->group(['prefix' => 'account', 'middleware' => ['auth']], function (Router $router) {
    $router->get('profile', 'AccountController@profile');
});

$router->group(['prefix' => 'auth'], function (Router $router) {
    $router->post('sign-in', 'AuthController@signIn');

    $router->post('sign-up', 'AuthController@signUp');

    $router->post('sign-out', [
        'middleware' => ['auth'],
        'uses' => 'AuthController@signOut',
    ]);
});

$router->get('captcha', 'BaseController@captcha');

$router->get('sitemap.xml', 'BaseController@sitemap');
