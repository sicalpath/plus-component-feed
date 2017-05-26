<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers\V2;

use DB;
use Carbon\Carbon;
use Zhiyi\Plus\Models\Digg;
use Zhiyi\Plus\Models\User;
use Illuminate\Http\Request;
use Zhiyi\Plus\Storages\Storage;
use Zhiyi\Plus\Models\StorageTask;
use Illuminate\Support\Facades\Auth;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedAtme;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedDigg;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Services\FeedCount;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedCollection;

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

    /**
     * 获取用户收藏列表.
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request [description]
     * @param  int     $user_id [description]
     * @return [type]           [description]
     */
    public function getUserCollection(Request $request)
    {
        $uid = $request->user()->id;
        $limit = $request->input('limit', 15);
        $max_id = intval($request->input('max_id'));

        $feeds = Feed::orderBy('id', 'DESC')
            ->where(function ($query) use ($max_id, $uid) {
                $query->whereIn('id', FeedCollection::where('user_id', $uid)->pluck('feed_id'));
                if ($max_id > 0) {
                    $query->where('id', '<', $max_id);
                }
            })
            ->byAudit()
            ->with(['storages', 'comments' => function ($query) {
                $query->orderBy('id', 'desc')
                    ->take(5)
                    ->select(['id', 'user_id', 'created_at', 'comment_content', 'reply_to_user_id', 'comment_mark'])
                    ->get();
            }, 'diggs' => function ($query) use ($uid) {
                $query->where('user_id', $uid)->first();
            }, 'collection' => function ($query) use ($uid) {
                $query->where('user_id', $uid)->first();
            }])
            ->take($limit)
            ->get();

        return $this->formatFeedList($feeds, $uid);
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

    /**
     * 增加分享浏览量.
     *
     * @author bs<414606094@qq.com>
     * @param  Feed $feed [description]
     */
    public function addFeedViewCount(Feed $feed)
    {
        $feed->increment('feed_view_count');

        return response()->json()->setStatusCode(201);
    }

    /**
     * 发送分享.
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request [description]
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (! $request->input('storage_task_ids') && ! $request->input('feed_content')) {
            return response()->json([
                'message' => ['动态内容不能为空'],
            ])->setStatusCode(400);
        }

        $storages = [];
        if ($request->input('storage_task_ids')) {
            $storageTasks = StorageTask::whereIn('id', $request->input('storage_task_ids'))->with('storage')->get();
            $storages = $storageTasks->map(function ($storageTask) {
                return $storageTask->storage->id;
            });
        }

        $feed = new Feed();
        $feed->feed_content = $request->input('feed_content') ?? '';
        $feed->feed_client_id = $request->getClientIp();
        $feed->user_id = $user->id;
        $feed->feed_from = $request->input('feed_from');
        $feed->feed_latitude = $request->input('latitude', '');
        $feed->feed_longtitude = $request->input('longtitude', '');
        $feed->feed_geohash = $request->input('geohash', '');
        $feed->feed_mark = $request->input('feed_mark', ($user->id.Carbon::now()->timestamp) * 1000); //默认uid+毫秒时间戳

        DB::beginTransaction();

        try {
            $feed->save();
            $feed->storages()->sync($storages);

            $user->storages()->sync($storages, false); // 更新作者的个人相册

            $request->isatuser == 1 && $this->analysisAtme($feed->feed_content, $feed->user_id, $feed->id);

            $count = new FeedCount();
            $count->count($user->id, 'feeds_count', 'increment'); //更新动态作者的动态数量

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();

            return response()->json([
                'message' => [$e->formatMessage()],
            ])->setStatusCode(500);
        }

        return response()->json($feed->id)->setStatusCode(201);
    }

    /**
     * 解析动态中的@用户.
     *
     * @author bs<414606094@qq.com>
     * @param  [type] $content [description]
     */
    protected function analysisAtme(string $content, int $user_id, int $feed_id)
    {
        $pattern = '/\[tsplus:(\d+):(\w+)\]/is';
        preg_match_all($pattern, $content, $matchs);
        $uids = $matchs[1];
        $time = Carbon::now();
        if (is_array($uids)) {
            $datas = array_map(function ($data) use ($user_id, $feed_id, $time) {
                return ['at_user_id' => $data, 'user_id' => $user_id, 'feed_id' => $feed_id, 'created_at' => $time, 'updated_at' => $time];
            }, $uids);

            FeedAtme::insert($datas); // 批量插入数据需要手动维护时间
        }
    }

    /**
     * 删除动态
     *
     * @author bs<414606094@qq.com>
     * @param  Feed $feed_id
     */
    public function delete(Request $request, Feed $feed)
    {
        $user_id = $request->user()->id;

        DB::beginTransaction();

        try {
            $comments = new FeedComment();
            $comments->where('feed_id', $feed->id)->delete(); // 删除相关评论

            $digg = new FeedDigg();
            $digg->where('feed_id', $feed->id)->delete(); // 删除相关点赞

            Digg::where(['component' => 'feed', 'digg_table' => 'feed_diggs', 'source_id' => $feed->id])->delete(); // 删除点赞总表记录

            $atme = new FeedAtme();
            $atme->where('feed_id', $feed->id)->delete(); // 删除@记录

            $collection = new FeedCollection();
            $collection->where('feed_id', $feed->id)->delete(); // 删除相关收藏

            $count = new FeedCount();
            $count->count($user_id, 'feeds_count', 'decrement'); // 更新动态作者的动态数量
            $count->count($user_id, 'diggs_count', 'decrement', $feed->feed_digg_count); // 更新动态作者收到的点赞数量

            $feed->delete();
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();

            return response()->json([
                'message' => [$e->formatMessage()],
            ])->setStatusCode(500);
        }

        return response()->json()->setStatusCode(204);
    }

    /**
     * 获取单条分享信息.
     *
     * @author bs<414606094@qq.com>
     * @param  Feed   $feed
     */
    public function getSingle(Feed $feed)
    {
        $uid = Auth::guard('api')->user()->id ?? 0;
        // 组装数据
        $data = [];
        // 用户标识
        $data['user_id'] = $feed->user_id;
        // 动态内容
        $data['feed'] = [];
        $data['feed']['feed_id'] = $feed->id;
        $data['feed']['feed_content'] = $feed->feed_content;
        $data['feed']['created_at'] = $feed->created_at->toDateTimeString();
        $data['feed']['feed_from'] = $feed->feed_from;
        $data['feed']['storages'] = $feed->storages->map(function ($storage) {
            return ['storage_id' => $storage->id, 'width' => $storage->image_width, 'height' => $storage->image_height];
        });
        // 工具栏数据
        $data['tool'] = [];
        $data['tool']['feed_view_count'] = $feed->feed_view_count;
        $data['tool']['feed_digg_count'] = $feed->feed_digg_count;
        $data['tool']['feed_comment_count'] = $feed->feed_comment_count;
        // 暂时剔除当前登录用户判定
        $data['tool']['is_digg_feed'] = $feed->diggs->where('user_id', $uid)->count();
        $data['tool']['is_collection_feed'] = $feed->collection->where('user_id', $uid)->count();
        // 动态评论,详情默认为空，自动获取评论列表接口
        $data['comments'] = [];
        // 动态最新8条点赞的用户id
        $data['diggs'] = $feed->diggs->take(8)->map(function ($digg) {
            return $digg->user_id;
        });

        $feed->increment('feed_view_count');

        return response()->json($data)->setStatusCode(200);
    }
}
