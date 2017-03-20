<?php
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers;

use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment;
use Carbon\Carbon;

class FeedCommentController extends Controller
{
	/**
	 * 查看一条微博的评论列表
	 * 
	 * @author bs<414606094@qq.com>
	 * @param  Request $request [description]
	 * @param  int     $feed_id [description]
	 * @return [type]           [description]
	 */
	public function getFeedCommentList(Request $request, int $feed_id)
	{
		$limit = $request->get('limit','15');
		$max_id = intval($request->get('max_id'));
        if(!$feed_id) {
            return response()->json([
                'status' => false,
                'code' => 6003,
                'message' => '动态ID不能为空'
            ])->setStatusCode(400);
        }
		$comments = FeedComment::byFeedId($feed_id)->take($limit)->where(function($query) use ($max_id) {
			if ($max_id > 0) {
				$query->where('id', '<', $max_id);
			}
		})->select(['id', 'created_at', 'comment_content', 'user_id', 'to_user_id', 'reply_to_user_id', 'comment_mark'])->orderBy('id','desc')->get();	

		if ($comments->isEmpty()) {
            return response()->json(static::createJsonData([
                'status' => true,
                'data' => [],
            ]))->setStatusCode(200);
		}
		$datas = $comments->toArray();

	    return response()->json(static::createJsonData([
	        'status' => true,
	        'data' => $datas,
	    ]))->setStatusCode(200);
	}

	/**
	 * 对一条动态或评论进行评论
	 * 
	 * @author bs<414606094@qq.com>
	 * @param  Request $request [description]
	 */
	public function addComment(Request $request, $feed_id)
	{	
        $feed = Feed::find($feed_id);
        if (!$feed) {
            return response()->json(static::createJsonData([
                'code' => 6004,
            ]))->setStatusCode(403);
        }
        $feedComment = new FeedComment();
		$feedComment->user_id = $request->user()->id;
		$feedComment->feed_id = $feed_id;
		$feedComment->to_user_id = $feed->user_id;
		$feedComment->reply_to_user_id = $request->reply_to_user_id ?? 0;
		$feedComment->comment_content = $request->comment_content;
		$feedComment->comment_mark = $request->input('comment_mark', ($request->user()->id.Carbon::now()->timestamp)*1000);//默认uid+毫秒时间戳
    	
    	$feedComment->save();
    	Feed::byFeedId($feed->id)->increment('feed_comment_count');//增加评论数量
		// $push = new Feedpush();
		// if ($push) {
		// 	$extras = ['action' => 'comment'];
		// 	$alert = '有人评论了你，去看看吧';
		// 	$audience = 'all';

		// 	$push->push($alert, $audience, $extras);
		// }
        return response()->json(static::createJsonData([
                'status' => true,
                'code' => 0,
                'message' => '评论成功',
                'data' => $feedComment->id
            ]))->setStatusCode(201);
	}

	/**
	 * 删除一条评论 
	 * 
	 * @author bs<414606094@qq.com>
	 * @param  Request $request    [description]
	 * @param  int     $comment_id [description]
	 * @return [type]              [description]
	 */
	public function delComment(Request $request, int $feed_id, int $comment_id)
	{
		if (FeedComment::where('id', $comment_id)->delete()) {
			Feed::byFeedId($feed_id)->decrement('feed_comment_count');//减少评论数量
		}
        return response()->json(static::createJsonData([
            'status' => true,
            'message' => '删除成功',
        ]))->setStatusCode(204);
	}

	/**
	 * 我收到的评论
	 * 
	 * @author bs<414606094@qq.com>
	 * @return [type] [description]
	 */
	public function myComment(Request $request)
	{
		$user_id = $request->user()->id;
		$limit = $request->input('limit', 15);
		$max_id = intval($request->input('max_id'));
		$comments = FeedComment::where(function ($query) use ($user_id) {
			$query->where('to_user_id', $user_id)->orwhere('reply_to_user_id', $user_id);
		})->where(function ($query) use ($max_id) {
			if ($max_id > 0) {
				$query->where('id', '<', $max_id);
			}
		})
		->take($limit)->with(['feed' => function ($query) {
			$query->select(['id', 'created_at', 'user_id', 'feed_content', 'feed_title'])->with(['storages' => function ($query) {
				$query->select(['feed_storage_id']);
			}]);
		}])->get();
		
        return response()->json(static::createJsonData([
            'status' => true,
            'message' => '获取成功',
            'data' => $comments,
        ]))->setStatusCode(200);
	}
}