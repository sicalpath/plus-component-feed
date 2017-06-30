<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/feeds')->group(function () {

    // 动态
    Route::get('/', 'FeedController@index');
    Route::get('/{feed}', 'FeedController@show');

    // 评论
    Route::get('/{feed}/comments', 'FeedCommentController@index');
    Route::get('/{feed}/comments/{comment}', 'FeedCommentController@show');

    // 点赞（喜欢）
    Route::get('/{feed}/diggs', 'FeedDiggController@index');

    /*
     * 需要授权的路由
     */
    Route::middleware('auth:api')->group(function () {

        // 动态
        Route::post('/', 'FeedController@store');
        Route::delete('/{feed}', 'FeedController@destroy');
        Route::patch('/{feed}/comment-paid', 'FeedPayController@commentPaid');

        // 评论
        Route::post('/{feed}/comments', 'FeedCommentController@store');
        Route::delete('/{feed}/comments/{comment}', 'FeedCommentController@destroy');

        // 点赞（喜欢）
        Route::post('/{feed}/diggs', 'FeedDiggController@store');
        Route::delete('/{feed}/undigg', 'FeedDiggController@destroy');
    });
});
