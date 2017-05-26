<?php

// 最新分享列表
Route::get('/feeds', 'FeedController@getNewFeeds');

// 热门分享
Route::get('/feeds/hots', 'FeedController@getHotFeeds');

// 某个用户的分享列表
Route::get('/user/{user}/feeds', 'FeedController@getUserFeeds')->where(['user' => '[0-9]+']);

// 获取单条动态
Route::get('/feed/{feed}', 'FeedController@getSingle');

Route::middleware('auth:api')
    ->group(function () {
        // 关注分享列表
        Route::get('/feeds/follows', 'FeedController@getFollowFeeds');
        // 登录用户收藏列表
        Route::get('/feeds/collections', 'FeedController@getUserCollection');
        // 增加分享浏览量
        Route::patch('/feed/{feed}/viewcount', 'FeedController@addFeedViewCount');
        // 发送分享
        Route::post('/feed', 'FeedController@store');
        // 删除分享
        Route::delete('/feed/{feed}', 'FeedController@delete');
    });
