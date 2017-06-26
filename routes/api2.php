<?php

use Illuminate\Support\Facades\Route;

// 动态
Route::get('/feeds', 'FeedController@index');
Route::get('/feeds/{feed}', 'FeedController@show');
Route::middleware('auth:api')->group(function () {
    Route::post('/feeds', 'FeedController@store');
    Route::patch('/feeds/{feed}/comment-paid', 'FeedPayController@commentPaid');
});
