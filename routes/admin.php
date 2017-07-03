<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@show')->name('feed:admin');
Route::get('/statistics', 'HomeController@statistics');
Route::get('/feeds', 'FeedController@index');
Route::delete('/feeds/{feed}', 'FeedController@deleteFeed');
Route::patch('/feeds/{feed}/review', 'FeedController@reviewFeed');
Route::get('/comments', 'CommentController@show');
Route::delete('/comments/{comment}', 'CommentController@delete');
