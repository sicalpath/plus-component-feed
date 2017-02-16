<?php
use Zhiyi\Plus\Http\Middleware;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Middleware as FeedMiddleware;

//分享列表
Route::get('/feeds', 'FeedController@index');
//获取一条动态的赞的用户列表
Route::get('/feeds/{feed_id}/diggusers','FeedDiggController@getDiggList');
//获取一条动态的赞的用户列表
Route::get('/feeds/{feed_id}/comments','FeedCommentController@getFeedCommentList');

Route::group([
	'middleware' => [
		'auth:api',
	]
], function() {
	//分享详情
	Route::get('/feeds/{feed_id}', 'FeedController@read');
	// //发送分享
	Route::post('/feeds', 'FeedController@store');
	// 添加评论
	Route::post('/feeds/{feed_id}/comment', 'FeedCommentController@addComment')
	->middleware(FeedMiddleware\CheckFeedByFeedId::class) //验证动态是否存在
	// ->middleware(FeedMiddleware\CheckReplyUser::class)  //验证被回复者是否存在 //回复动态时 此值为0
	->middleware(FeedMiddleware\VerifyCommentContent::class); // 验证评论内容
	// //删除分享
	// Route::delete('/feeds/{feed_id}', 'xxx@xxx');
	// //评论列表
	// Route::get('/feeds/{feed_id}/comments', 'xxx@xxx');
	//删除评论 TODO 根据权限及实际需求增加中间件
	Route::delete('/feeds/{feed_id}/comment/{comment_id}', 'FeedCommentController@delComment');
	// //点赞
	Route::post('/feeds/{feed_id}/digg', 'FeedDiggController@diggFeed');
	// //取消点赞
	Route::delete('/feeds/{feed_id}/digg', 'FeedDiggController@cancelDiggFeed');
	//获取@我的分享列表
	Route::get('/atme/feeds', 'FeedAtmeController@getAtmeList');
});
