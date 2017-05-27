<?php

// 最新分享列表
Route::get('/feeds', 'FeedController@getNewFeeds');

// 热门分享
Route::get('/feeds/hots', 'FeedController@getHotFeeds');

// 某个用户的分享列表
Route::get('/user/{user}/feeds', 'FeedController@getUserFeeds')->where(['user' => '[0-9]+']);

// 获取单条动态
Route::get('/feed/{feed}', 'FeedController@getSingle')->where(['feed' => '[0-9]+']);

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

        // 获取@我的分享列表
        Route::get('/feeds/atme', 'FeedAtmeController@get');

        // 收藏分享
        Route::post('/feed/{feed}/collection', 'FeedCollectionController@add');

        // 删除收藏
        Route::delete('/feed/{feed}/collection', 'FeedCollectionController@delete');

        // 点赞分享
        Route::post('/feed/{feed}/digg', 'FeedDiggController@add');

        // 取消点赞
        Route::delete('/feed/{feed}/digg', 'FeedDiggController@delete');

        // 对一条分享点赞的列表
        Route::get('/feed/{feed}/diggs', 'FeedDiggController@getDiggs');

        // 我收到的点赞
        Route::get('/feeds/diggs', 'FeedDiggController@getMy');
    });
