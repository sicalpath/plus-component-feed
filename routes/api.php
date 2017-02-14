<?php
use Zhiyi\Plus\Http\Middleware;

//分享列表
// Route::get('/feeds', 'xxx@xxx');
//获取一条动态的赞列表
Route::get('/feeds/{feed_id}/diggusers','FeedDiggApiController@getDiggList');


Route::group([
	'middleware' => [
		'auth:api',
	]
], function() {
	//分享详情
	Route::get('/feed/{feed_id}', 'FeedController@read');
	// //发送分享
	Route::post('/feed', 'FeedController@store');
	// //删除分享
	// Route::delete('/feeds/{feed_id}', 'xxx@xxx');
	// //评论列表
	// Route::get('/feeds/{feed_id}/comments', 'xxx@xxx');
	// //发送评论
	// Route::post('/feeds/{feed_id}/comments', 'xxx@xxx');
	// //删除评论
	// Route::delete('/feeds/{feed_id}/comments/{comment_id}', 'xxx@xxx');
	// //点赞
	Route::post('/feeds/{feed_id}/digg', 'FeedDiggApiController@diggFeed');
	// //取消点赞
	Route::delete('/feeds/{feed_id}/digg', 'FeedDiggApiController@cancelDiggFeed');
	//获取@我的分享列表
	Route::get('/feeds/atme', 'FeedAtmeApiController@getAtmeList');
});
