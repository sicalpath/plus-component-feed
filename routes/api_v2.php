<?php

// 最新分享列表
Route::get('/feeds', 'FeedController@getNewFeeds');

// 热门分享
Route::get('/feeds/hots', 'FeedController@getHotFeeds');

// 某个用户的分享列表
Route::get('/user/{user}/feeds', 'FeedController@getUserFeeds')->where(['user' => '[0-9]+']);

Route::middleware('auth:api')
	->prefix('/feeds')
	->group(function () {
		// 关注分享列表
		Route::get('/follows', 'FeedController@getFollowFeeds');
		// 登录用户收藏列表
		Route::get('/collections', 'FeedController@getUserCollection');
	});