<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers\V2;

use DB;
use Zhiyi\Plus\Models\Digg;
use Illuminate\Http\Request;
use Zhiyi\Plus\Jobs\PushMessage;
use Illuminate\Database\QueryException;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedDigg;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedStorage;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Services\FeedCount;

class FeedDiggController extends Controller
{
    /**
     * 获取赞微博的用户.
     *
     * @author bs<414606094@qq.com>
     * @return json
     */
    public function getDiggs(Request $request, Feed $feed)
    {
        $limit = $request->input('limit', 10);
        $max_id = $request->input('max_id');
        $feed->load([
                'diggs' => function ($query) use ($limit, $max_id) {
                    $query->where(function ($query) use ($max_id) {
                        if ($max_id > 0) {
                            $query->where('id', '<', $max_id);
                        }
                    })->take($limit)->orderBy('id', 'desc');
                }
            ]);

        $users = [];
        $feed->diggs->each(function($digg) use (&$users) {
            $users[] = [
                'feed_digg_id' => $digg->id,
                'user_id' => $digg->user_id,
            ];
        });

        return response()->json($users)->setStatusCode(200);
    }

    /**
     * 点赞一个动态
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request [description]
     * @param  int     $feed_id [description]
     * @return [type]           [description]
     */
    public function add(Request $request, Feed $feed)
    {
        $user_id = $request->user()->id;
        if ($feed->diggs->where('user_id', $user_id)->first()) {
            return response()->json([
                'message' => ['已赞过该动态'],
            ])->setStatusCode(400);
        }

        DB::beginTransaction();

        try {
            $digg = $feed->diggs()->create(['user_id' => $user_id]);

            $feed->increment('feed_digg_count'); //增加点赞数量

            $count = new FeedCount();
            $count->count($feed->user_id, 'diggs_count', $method = 'increment'); //更新动态作者收到的赞数量

            Digg::create(['component' => 'feed',
                        'digg_table' => 'feed_diggs',
                        'digg_id' => $digg->id,
                        'source_table' => 'feeds',
                        'source_id' => $feed->id,
                        'source_content' => $feed->feed_content,
                        'source_cover' => 0,
                        'user_id' => $user_id,
                        'to_user_id' => $feed->user_id,
                        ]); // 统计到点赞总表

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();

            return response()->json([
                'message' => [$e->errorInfo],
            ])->setStatusCode(500);
        }

        $extras = ['action' => 'digg'];
        $alert = '有人赞了你的动态，去看看吧';
        $alias = $feed->user_id;

        dispatch(new PushMessage($alert, (string) $alias, $extras));

        return response()->json()->setStatusCode(201);
    }

    /**
     * 取消点赞一个动态
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request [description]
     * @param  int     $feed_id [description]
     * @return [type]           [description]
     */
    public function delete(Request $request, Feed $feed)
    {
        $user_id = $request->user()->id;
        $digg = $feed->diggs->where('user_id', $user_id)->first();
        if (! $digg) {
            return response()->json([
                'message' => ['未对该动态点赞'],
            ])->setStatusCode(404);
        }

        DB::transaction(function () use ($digg, $feed, $user_id) {
            $digg->delete();
            $feed->decrement('feed_digg_count'); //减少点赞数量

            $count = new FeedCount();
            $count->count($feed->user_id, 'diggs_count', 'decrement'); //更新动态作者收到的赞数量

            Digg::where(['component' => 'feed', 'digg_id' => $digg->id])->delete(); // 统计到点赞总表
        });

        return response()->json()->setStatusCode(204);
    }

    /**
     * 我收到的赞.
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getMy(Request $request)
    {
        $user_id = $request->user()->id;
        $limit = $request->input('limit', 15);
        $max_id = intval($request->input('max_id'));

        $diggs = FeedDigg::join('feeds', function ($query) use ($user_id) {
            $query->on('feeds.id', '=', 'feed_diggs.feed_id')->where('feeds.user_id', $user_id);
        })
        ->select(['feed_diggs.id', 'feed_diggs.user_id', 'feed_diggs.created_at', 'feed_diggs.feed_id', 'feeds.feed_content', 'feeds.feed_title'])
        ->where(function ($query) use ($max_id) {
            if ($max_id > 0) {
                $query->where('feed_diggs.id', '<', $max_id);
            }
        })
        ->take($limit)
        ->with(['feed.storages' => function ($query) {
            $query->select('feed_storage_id');
        }])
        ->orderBy('id', 'desc')
        ->get()->toArray();

        foreach ($diggs as &$digg) {
            $digg['storages'] = array_map(function (&$storage){
                return [ 
                    'feed_storage_id' => $storage['feed_storage_id'],
                ];
            }, $digg['feed']['storages']);
            unset($digg['feed']);
        }

        return response()->json($diggs)->setStatusCode(200);
    }
}
