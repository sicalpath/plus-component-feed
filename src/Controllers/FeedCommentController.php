<?php
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers;

use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment;

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
		})->select(['id', 'created_at', 'comment_content', 'user_id', 'to_user_id', 'reply_to_user_id'])->orderBy('id','desc')->get();	

		if ($comments->isEmpty()) {
            return response()->json(static::createJsonData([
                'status' => true,
                'data' => [],
            ]))->setStatusCode(200);
		}
		$datas = $comments->map(function ($comment) {
			return array_merge($comment->toArray(), [
				'created_at' => $comment->created_at->timestamp,
			]);
		});

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
		$feedComment['user_id'] = $request->user()->id;
		$feedComment['feed_id'] = $feed_id;
		$feedComment['to_user_id'] = $feed->user_id;
		$feedComment['reply_to_user_id'] = $request->reply_to_user_id ?? 0;
		$feedComment['comment_content'] = $request->comment_content;
    	FeedComment::create($feedComment);
    	Feed::byFeedId($feed->id)->increment('feed_comment_count');//增加评论数量
        return response()->json(static::createJsonData([
                'status' => true,
                'code' => 0,
                'message' => '评论成功'
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
	public function delComment(Request $request, int $comment_id, int $feed_id)
	{
		FeedComment::where('id', $comment_id)->delete();
		Feed::byFeedId($feed_id)->decrement('feed_comment_count');//减少评论数量
        return response()->json(static::createJsonData([
            'status' => true,
            'message' => '删除成功',
        ]))->setStatusCode(201);
	}
}