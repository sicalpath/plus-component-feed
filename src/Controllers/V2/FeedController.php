<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers\V2;

use DB;
use Carbon\Carbon;
use Zhiyi\Plus\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedDigg;

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
        $limit = $request->input('limit') ?: 15;

        $feeds = Feed::where(function ($query) use ($request) {
            if ($request->input('max_id') > 0) {
                $query->where('id', '<', $request->input('max_id'));
            }
        })
        ->orderBy('id', 'DESC')
        ->with(['storages', 'comments' => function ($query) {
            $query->orderBy('id', 'desc')
                ->take(5)
                ->select(['id', 'user_id', 'created_at', 'comment_content', 'reply_to_user_id', 'comment_mark'])
                ->get();
        }, 'diggs' => function ($query) use ($user_id) {
            $query->where('user_id', $user_id)->first();
        }, 'collection' => function ($query) use ($user_id) {
            $query->where('user_id', $user_id)->first();
        }])
        ->take($limit)
        ->get();

        return $this->formatFeedList($feeds, $user_id);
    }

    /**
     * 获取关注动态列表.
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request [description]
     */
    public function getFollowFeeds(Request $request)
    {
        $user_id = Auth::guard('api')->user()->id;
        // 设置单页数量
        $limit = $request->input('limit') ?? 15;
        $feeds = Feed::orderBy('id', 'DESC')
            ->whereIn('user_id', array_merge([$user_id], $request->user()->follows->pluck('following_user_id')->toArray()))
            ->where(function ($query) use ($request) {
                if ($request->max_id > 0) {
                    $query->where('id', '<', $request->max_id);
                }
            })
            ->byAudit()
            ->with(['storages', 'comments' => function ($query) {
                $query->orderBy('id', 'desc')
                    ->take(5)
                    ->select(['id', 'user_id', 'created_at', 'comment_content', 'reply_to_user_id', 'comment_mark'])
                    ->get();
            }, 'diggs' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->first();
            }, 'collection' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->first();
            }])
            ->take($limit)
            ->get();

        return $this->formatFeedList($feeds, $user_id);
    }

    /**
     * 热门动态列表构建.
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getHotFeeds(Request $request)
    {
        $user_id = Auth::guard('api')->user()->id ?? 0;
        // 设置单页数量
        $limit = $request->limit ?? 15;
        $page = $request->page ?? 1;
        $skip = ($page - 1) * $limit;

        $feeds = Feed::orderBy('id', 'DESC')
            ->whereIn('id', FeedDigg::groupBy('feed_id')
                ->take($limit)
                ->select('feed_id', DB::raw('COUNT(user_id) as diggcount'))
                ->where('created_at', '>', Carbon::now()->subMonth()->toDateTimeString())
                ->orderBy('diggcount', 'desc')
                ->orderBy('feed_id', 'desc')
                ->skip($skip)
                ->pluck('feed_id')
                )
            ->byAudit()
            ->with(['storages', 'comments' => function ($query) {
                $query->orderBy('id', 'desc')
                    ->take(5)
                    ->select(['id', 'user_id', 'created_at', 'comment_content', 'reply_to_user_id', 'comment_mark'])
                    ->get();
            }, 'diggs' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->first();
            }, 'collection' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->first();
            }])
            ->get();

        return $this->formatFeedList($feeds, $user_id);
    }

    /**
     * 获取单个用户的动态列表.
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getUserFeeds(Request $request, User $user)
    {
        $auth_id = Auth::guard('api')->user()->id ?? 0;
        $limit = $request->input('limit', 15);
        $max_id = intval($request->input('max_id'));

        $feeds = Feed::orderBy('id', 'DESC')
            ->where('user_id', $user->id)
            ->where(function ($query) use ($max_id) {
                if ($max_id > 0) {
                    $query->where('id', '<', $max_id);
                }
            })
            ->with(['storages', 'comments' => function ($query) {
                $query->orderBy('id', 'desc')
                    ->take(5)
                    ->select(['id', 'user_id', 'created_at', 'comment_content', 'reply_to_user_id', 'comment_mark'])
                    ->get();
            }, 'diggs' => function ($query) use ($auth_id) {
                $query->where('user_id', $auth_id)->first();
            }, 'collection' => function ($query) use ($auth_id) {
                $query->where('user_id', $auth_id)->first();
            }])
        ->byAudit()
        ->take($limit)
        ->get();

        return $this->formatFeedList($feeds, $auth_id);
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
            $data['tool']['is_digg_feed'] = $feed->diggs->isEmpty() ? 0 : 1;
            $data['tool']['is_collection_feed'] = $feed->collection->isEmpty() ? 0 : 1;
            // 最新3条评论
            $data['comments'] = $feed->comments;
            $datas[] = $data;
        });

        return response()->json($datas)->setStatusCode(200);
    }
}
