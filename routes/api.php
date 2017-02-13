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
	//删除分享
	Route::delete('/feeds/{feed_id}', 'xxx@xxx');
	//评论列表
	Route::get('/feeds/{feed_id}/comments', 'xxx@xxx');
	//发送评论
	Route::post('/feeds/{feed_id}/comments', 'xxx@xxx');
	//删除评论
	Route::delete('/feeds/{feed_id}/comments/{comment_id}', 'xxx@xxx');
	//点赞
	Route::post('/feeds/{feed_id}/diggs', 'xxx@xxx');
	//取消点赞
	Route::delete('/feeds/{feed_id}/diggs', 'xxx@xxx');
	//获取@我的分享列表
	Route::get('/feeds/atme', 'xxx@xxx');
});
