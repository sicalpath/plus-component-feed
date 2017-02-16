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
		})->with(['user','replyUser'])->orderBy('id','desc')->get();	

		if ($comments->isEmpty()) {
            return response()->json(static::createJsonData([
                'status' => true,
                'data' => [],
            ]))->setStatusCode(200);
		}

		foreach ($comments as $key => $value) {
			$data['comment_id'] = $value->id; 
			$data['create_at'] = $value->created_at->timestamp;
			$data['comment_content'] = $value->comment_content;
			$data['user']['name'] = $value->user->name;
			$data['user']['phone'] = $value->user->phone;
			$data['user']['email'] = $value->user->email ?? '';
			$data['replyUser']['name'] = $value->replyUser->name;
			$data['replyUser']['phone'] = $value->replyUser->phone;
			$data['replyUser']['email'] = $value->replyUser->email ?? '';

			$datas[] = $data;
		}
	    return response()->json(static::createJsonData([
	        'status' => true,
	        'data' => $datas,
	    ]))->setStatusCode(200);
	}

	public function addComment(Request $request)
	{	
		$feed = $request->attributes->get('feed');

		$feedComment['user_id'] = $request->user()->id;
		$feedComment['feed_id'] = $feed->id;
		$feedComment['to_user_id'] = $feed->user_id;
		$feedComment['reply_to_user_id'] = $request->reply_to_user_id ?? 0;
		$feedComment['comment_content'] = $request->comment_content;

    	FeedComment::create($feedComment);

        return response()->json([
                'status' => true,
                'code' => 0,
                'message' => '评论成功'
            ])->setStatusCode(201);
	}
}