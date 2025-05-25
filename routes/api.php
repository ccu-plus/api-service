<?php

declare(strict_types=1);

use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\IpController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'courses', 'namespace' => 'App\Http\Controllers'], function (): void {
    Route::get('search', 'CourseController@search');

    Route::get('comments', 'CommentController@latest');

    Route::get('statistics', 'CommentController@statistics');

    Route::get('{code}', 'CourseController@show');

    Route::group(['prefix' => '{code}/comments'], function (): void {
        Route::get('/', 'CommentController@index');

        Route::post('/', 'CommentController@store');
    });
});

Route::get('captcha')->uses(CaptchaController::class)->name('captcha');

Route::get('sitemap')->uses(SitemapController::class)->name('sitemap');

Route::get('ip')->uses(IpController::class)->name('ip');
