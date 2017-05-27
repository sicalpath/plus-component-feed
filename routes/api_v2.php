<?php

use Zhiyi\Plus\Http\Middleware;

// 最新分享列表
Route::get('/feeds', 'FeedController@getNewFeeds');

// 热门分享
Route::get('/feeds/hots', 'FeedController@getHotFeeds');

// 某个用户的分享列表
Route::get('/user/{user}/feeds', 'FeedController@getUserFeeds')->where(['user' => '[0-9]+']);

// 获取单条动态
Route::get('/feed/{feed}', 'FeedController@getSingle')->where(['feed' => '[0-9]+']);

// 对一条分享点赞的列表
Route::get('/feed/{feed}/diggs', 'FeedDiggController@getDiggs');

// 获取一条分享的评论列表
Route::get('/feed/{feed}/comments', 'FeedCommentController@getList');

Route::middleware('auth:api')
    ->group(function () {
        // 关注分享列表
        Route::get('/feeds/follows', 'FeedController@getFollowFeeds');

        // 登录用户收藏列表
        Route::get('/feeds/collections', 'FeedController@getUserCollection');

        // 增加分享浏览量
        Route::patch('/feed/{feed}/viewcount', 'FeedController@addFeedViewCount');

        // 发送分享
        Route::post('/feed', 'FeedController@store')
            ->middleware('role-permissions:feed-post,你没有发布分享的权限');

        // 删除分享
        Route::delete('/feed/{feed}', 'FeedController@delete');

        // 获取@我的分享列表
        Route::get('/feeds/atme', 'FeedAtmeController@get');

        // 收藏分享
        Route::post('/feed/{feed}/collection', 'FeedCollectionController@add');

        // 删除收藏
        Route::delete('/feed/{feed}/collection', 'FeedCollectionController@delete');

        // 点赞分享
        Route::post('/feed/{feed}/digg', 'FeedDiggController@add')
            ->middleware('role-permissions:feed-digg,你没有点赞分享的权限');

        // 取消点赞
        Route::delete('/feed/{feed}/digg', 'FeedDiggController@delete');

        // 我收到的点赞
        Route::get('/feeds/diggs', 'FeedDiggController@getMy');

        // 发送评论
        Route::post('/feed/{feed}/comment', 'FeedCommentController@add')
            ->middleware('role-permissions:feed-comment,你没有评论分享的权限');

        // 删除评论
        Route::delete('/feed/comment/{comment}', 'FeedCommentController@delete');

        // 根据id或当前账户查询评论
        Route::get('/feeds/comments', 'FeedCommentController@search');
    });
