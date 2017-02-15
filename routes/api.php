<?php
use Zhiyi\Plus\Http\Middleware;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Middleware as FeedMiddleware;

//分享列表
Route::get('/feeds', 'FeedController@index');
//获取一条动态的赞的用户列表
Route::get('/feeds/{feed_id}/diggusers','FeedDiggController@getDiggList');

Route::group([
	'middleware' => [
		'auth:api',
	]
], function() {
	//分享详情
	Route::get('/feeds/{feed_id}', 'FeedController@read');
	// //发送分享
	Route::post('/feeds', 'FeedController@store');
	// //删除分享
	// Route::delete('/feeds/{feed_id}', 'xxx@xxx');
	// //评论列表
	// Route::get('/feeds/{feed_id}/comments', 'xxx@xxx');
	// //发送评论
	// Route::post('/feeds/{feed_id}/comments', 'xxx@xxx');
	// //删除评论
	// Route::delete('/feeds/{feed_id}/comments/{comment_id}', 'xxx@xxx');
	// //点赞
	Route::post('/feeds/{feed_id}/digg', 'FeedDiggController@diggFeed');
	// //取消点赞
	Route::delete('/feeds/{feed_id}/digg', 'FeedDiggController@cancelDiggFeed');
	//获取@我的分享列表
	Route::get('/feeds/atme', 'FeedAtmeController@getAtmeList');
});
