<?php
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers;

use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;

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
}