<?php
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers;

use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedDigg;

class FeedDiggApiController extends Controller
{
	/**
	 * 获取赞微博的与用户
	 * 
	 * @author bs<414606094@qq.com>
	 * @return json
	 */	
	public function getDiggList(Request $request, int $feed_id)
	{
		$feed = Feed::byFeedId($feed_id)->first();
		if (!$feed) {
            return response()->json(static::createJsonData([
            	'code' => 6004,
                'status' => false,
                'message' => '指定动态不存在',
            ]))->setStatusCode(404);
		}
		if (!($feed->diggs->toArray())) {
            return response()->json(static::createJsonData([
                'status' => true,
                'data' => [],
            ]))->setStatusCode(200);
		}
		foreach ($feed->diggs as $key => $value) {
			if ($value->user->toArray()) {
				$user['name'] = $value->user->name;
				$user['phone'] = $value->user->phone;
				$user['email'] = $value->user->email ?? '';

				$users[] = $user;
			}
		}
	    return response()->json(static::createJsonData([
	        'status' => true,
	        'data' => $users,
	    ]))->setStatusCode(200);
	}	

	/**
	 * 点赞一个动态
	 * 
	 * @author bs<414606094@qq.com>
	 * @param  Request $request [description]
	 * @param  int     $feed_id [description]
	 * @return [type]           [description]
	 */
	public function diggFeed(Request $request, int $feed_id)
	{
		$feeddigg['user_id'] = $request->user()->id;
		$feeddigg['feed_id'] = $feed_id;
		if (FeedDigg::byFeedId($feed_id)->byUserId($feeddigg['user_id'])->first()) {
            return response()->json(static::createJsonData([
            	'code' => 6005,
                'status' => false,
                'message' => '已赞过该动态',
            ]))->setStatusCode(400);
		}

		FeedDigg::create($feeddigg);
        return response()->json(static::createJsonData([
            'status' => true,
            'message' => '点赞成功',
        ]))->setStatusCode(201);
	}

	/**
	 * 取消点赞一个动态
	 * 
	 * @author bs<414606094@qq.com>
	 * @param  Request $request [description]
	 * @param  int     $feed_id [description]
	 * @return [type]           [description]
	 */
	public function cancelDiggFeed(Request $request, int $feed_id)
	{
		$feeddigg['user_id'] = $request->user()->id;
		$feeddigg['feed_id'] = $feed_id;
		if (!FeedDigg::byFeedId($feed_id)->byUserId($feeddigg['user_id'])->first()) {
            return response()->json(static::createJsonData([
            	'code' => 6006,
                'status' => false,
                'message' => '未对该动态点赞',
            ]))->setStatusCode(400);
		}

		FeedDigg::byFeedId($feed_id)->byUserId($feeddigg['user_id'])->delete();
        return response()->json(static::createJsonData([
            'status' => true,
            'message' => '取消点赞成功',
        ]))->setStatusCode(204);
	}


}