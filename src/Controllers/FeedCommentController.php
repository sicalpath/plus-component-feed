<?php
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers;

use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment;

class FeedCommentController extends Controller
{
	public function getFeedCommentList(Request $request, int $feed_id)
	{
		$limit = $request->get('limit','15');
		$max_id = intval($request->get('max_id'));
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
}