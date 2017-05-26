<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers\V2;

use Illuminate\Http\Request;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedCollection;

class FeedCollectionController extends Controller
{
    /**
     * 收藏一条动态
     *
     * @author bs<414606094@qq.com>
     * @param  [type] $feed_id [description]
     */
    public function add(Request $request, Feed $feed)
    {
        $user_id = $request->user()->id;
        if ($feed->collection->where('user_id', $user_id)->first()) {
            return response()->json([
                'message' => ['已收藏该动态'],
            ])->setStatusCode(400);
        }

        $feed->collection()->create(['user_id' => $user_id]);

        return response()->json()->setStatusCode(201);
    }

    /**
     * 取消收藏一条动态
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request [description]
     * @param  int     $feed_id [description]
     * @return [type]           [description]
     */
    public function delete(Request $request, Feed $feed)
    {
        $user_id = $request->user()->id;

        if (!$feed->collection->where('user_id', $user_id)->first()) {
            return response()->json([
                'message' => ['未对该动态收藏'],
            ])->setStatusCode(404);
        }

        $feed->collection()->where('user_id', $user_id)->delete();

        return response()->json()->setStatusCode(204);
    }
}
