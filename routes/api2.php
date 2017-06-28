<?php

use Illuminate\Support\Facades\Route;

// 动态
Route::get('/feeds', 'FeedController@index');
Route::get('/feeds/{feed}', 'FeedController@show');
Route::middleware('auth:api')->prefix('/feeds')->group(function () {
    Route::post('/', 'FeedController@store');
    Route::delete('/{feed}', 'FeedController@destroy');
    Route::patch('/{feed}/comment-paid', 'FeedPayController@commentPaid');
});

// 获取评论
Route::get('/feeds/{feed}/comments', 'FeedCommentController@index');
Route::post('/feeds/{feed}/comments', 'FeedCommentController@store');
Route::get('/feeds/{feed}/comments/{comment}', 'FeedCommentController@show');
Route::delete('/feeds/{feed}/comments/{comment}', 'FeedCommentController@destroy');
