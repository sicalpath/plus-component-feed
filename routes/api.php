<?php

use Zhiyi\Plus\Http\Middleware;

//分享列表
Route::get('/feeds', 'xxx@xxx');

Route::group([
	'middleware' => [
		Middleware\AuthUserToken::class,
	]
], function($route) {
	//分享详情
	Route::get('/feeds/{feed_id}', 'xxx@xxx');
	//发送分享
	Route::post('/feeds', 'xxx@xxx');
	
});

