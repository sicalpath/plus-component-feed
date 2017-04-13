<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@show')->name('feed:admin');

Route::middleware('auth')->group(function () {
    Route::get('/statistics', 'HomeController@statistics');
    Route::get('/feeds', 'FeedController@showFeeds');
    Route::delete('/feeds/{feed}', 'FeedController@deleteFeed');
    Route::patch('/feeds/{feed}/review', 'FeedController@reviewFeed');
});
