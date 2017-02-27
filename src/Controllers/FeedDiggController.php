<?php
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers;

use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedDigg;
use Zhiyi\Plus\Models\UserDatas;

class FeedDiggController extends Controller
{
	/**
	 * 获取赞微博的用户
	 * 
	 * @author bs<414606094@qq.com>
	 * @return json
	 */	
	public function getDiggList(Request $request, int $feed_id)
	{
		$limit = $request->get('limit', 10);
		//intval($request->limit) ? : 10;
		$max_id = $request->get('max_id');
		$feed = Feed::byFeedId($feed_id)
			->with([
				'diggs' => function ($query) use ($limit, $max_id) {
					if (intval($max_id) > 0)  {
						$query->where('id', '<', intval($max_id));
					}
					$query->take($limit);
				},
				'diggs.user'
			])
			->orderBy('id', 'desc')->first();
		if (!$feed) {
            return response()->json(static::createJsonData([
            	'code' => 6004,
                'status' => false,
                'message' => '指定动态不存在',
            ]))->setStatusCode(404);
		}

		if ($feed->diggs->isEmpty()) {
            return response()->json(static::createJsonData([
                'status' => true,
                'data' => [],
            ]))->setStatusCode(200);
		}
		foreach ($feed->diggs as $key => $value) {
				$user['feed_digg_id'] = $value->id;
				$user['user_id'] = $value->user_id;

				$users[] = $user;
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
		$feed = Feed::find($feed_id);
		if (!$feed) {
            return response()->json(static::createJsonData([
                'code' => 6004,
            ]))->setStatusCode(403);
		}
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
		Feed::byFeedId($feed_id)->increment('feed_digg_count');//增加点赞数量
		$this->countdigg($feed->user_id, $method = 'increment');//更新动态作者收到的赞数量
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
		$feed = Feed::find($feed_id);
		if (!$feed) {
            return response()->json(static::createJsonData([
                'code' => 6004,
            ]))->setStatusCode(403);
		}
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
		Feed::byFeedId($feed_id)->decrement('feed_digg_count');//减少点赞数量
		$this->countdigg($feed->user_id, $method = 'decrement');//更新动态作者收到的赞数量
        return response()->json(static::createJsonData([
            'status' => true,
            'message' => '取消点赞成功',
        ]))->setStatusCode(204);
	}

	/**
	 * 对用户收到的赞数进行统计
	 * 
	 * @author bs<414606094@qq.com>
	 * @param  [type] $uid    [description]
	 * @param  string $method increment -点赞统计数加1  decrement - 点赞统计数减1
	 * @return [type]         [description]
	 */
	protected function countdigg($uid, $method = 'increment')
	{
		$key = 'diggs_count';
		$datas = false;
		if ($method == 'increment' || $method == 'decrement') {
			if (!UserDatas::byKey($key)->byUserId($uid)->first()) {
				$insert = ['key' => $key, 'user_id' => $uid, 'value' => 0];
				UserDatas::create($insert);
			}
		
			$datas = UserDatas::where('key', $key)->byUserId($uid)->$method('value');
		}
		
		return $datas;
	}
}