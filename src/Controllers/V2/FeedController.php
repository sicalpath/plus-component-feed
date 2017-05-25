<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers\V2;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;

class FeedController extends Controller
{
	/**
	 * 获取最新动态列表. 
	 *
	 * @author bs<414606094@qq.com>
	 * @param  Request $request 
	 */
	public function getNewFeeds(Request $request)
	{
		$user_id = Auth::guard('api')->user()->id ?? 0;
		$limit = $request->input('limit') ? : 15;

		$feeds = Feed::where(function ($query) use ($request) {
			if ($request->input('max_id') > 0) {
				$query->where('id', '<', $request->input('max_id'));
			}	
		})
		->orderBy('id', 'DESC')
		->withCount(['diggs' => function ($query) use ($user_id) {
			if ($user_id) {
				$query->where('user_id', $user_id);
			}
		}])
		->with(['storages', 'comments' => function ($query) {
			$query->orderBy('id', 'desc')
				->take(5)
				->select(['id', 'user_id', 'created_at', 'comment_content', 'reply_to_user_id', 'comment_mark'])
				->get();
		}])
		->take($limit)
		->get();

		return $this->formatFeedList($feeds, $user_id);
	}



	protected function formatFeedList($feeds, $uid)
	{
		$datas = [];
		$feeds->each(function ($feed) use (&$datas, $uid) {
			$data = [];
            $data['user_id'] = $feed->user_id;
            $data['feed_mark'] = $feed->feed_mark;
            // 动态数据
            $data['feed'] = [];
            $data['feed']['feed_id'] = $feed->id;
            $data['feed']['feed_title'] = $feed->feed_title ?? '';
            $data['feed']['feed_content'] = $feed->feed_content;
            $data['feed']['created_at'] = $feed->created_at->toDateTimeString();
            $data['feed']['feed_from'] = $feed->feed_from;
            $data['feed']['storages'] = $feed->storages->map(function ($storage) {
                return ['storage_id' => $storage->id, 'width' => $storage->image_width, 'height' => $storage->image_height];
            });
            // 工具数据
            $data['tool'] = [];
            $data['tool']['feed_view_count'] = $feed->feed_view_count;
            $data['tool']['feed_digg_count'] = $feed->feed_digg_count;
            $data['tool']['feed_comment_count'] = $feed->feed_comment_count;
            $data['tool']['is_digg_feed'] = $uid ? FeedDigg::byFeedId($feed->id)->byUserId($uid)->count() : 0;
            $data['tool']['is_collection_feed'] = $uid ? FeedCollection::where('feed_id', $feed->id)->where('user_id', $uid)->count() : 0;
            // 最新3条评论
            $data['comments'] = $feed->comments;
			$datas[] = $data;
		});
		return response()->json($datas)->setStatusCode(200);
	}
}