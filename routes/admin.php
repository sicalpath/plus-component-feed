<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', 'HomeController@show')->name('feed:admin');
    Route::get('/statistics', 'HomeController@statistics');
    Route::get('/feeds', 'FeedController@showFeeds');
    Route::patch('/feeds/{feed}/review', 'FeedController@reviewFeed');
});
