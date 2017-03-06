<?php
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers;

use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Plus\Models\Storage as StorageModel;
use Zhiyi\Plus\Models\StorageTask;
use Zhiyi\Plus\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedDigg;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedCollection;
use Zhiyi\Plus\Storages\Storage;
use Validator;
use Carbon\Carbon;
use DB;

class FeedController extends Controller
{
    public function index($feeds, $uid)
    {   
        $datas = [];
        foreach($feeds as $feed) {
            $data = [];
            $data['user_id'] = $feed->user_id;
            $data['feed_mark'] = $feed->feed_mark;
            // 动态数据
            $data['feed'] = [];
            $data['feed']['feed_id'] = $feed->id;
            $data['feed']['feed_title'] = $feed->feed_title ?? '';
            $data['feed']['feed_content'] = $feed->feed_content;
            $data['feed']['created_at'] = $feed->created_at->timestamp;
            $data['feed']['feed_from'] = $feed->feed_from;
            $data['feed']['storages'] = $feed->storages->map(function($storage) {
                return [ 'storage_id' => $storage->id, 'width' => $storage->image_width, 'height' => $storage->image_height];
            });
            // 工具数据
            $data['tool'] = [];
            $data['tool']['feed_view_count'] = $feed->feed_view_count;
            $data['tool']['feed_digg_count'] = $feed->feed_digg_count;
            $data['tool']['feed_comment_count'] = $feed->feed_comment_count;
            // 暂时剔除当前登录用户判定
            $data['tool']['is_digg_feed'] = $uid ? FeedDigg::byFeedId($feed->id)->byUserId($uid)->count() : 0;
            $data['tool']['is_collection_feed'] = $uid ? FeedCollection::where('feed_id', $feed->id)->where('user_id', $uid)->count() : 0;
            // 最新3条评论
            $data['comments'] = [];
            foreach($feed->comments()->orderBy('id', 'DESC')->take(3)->get() as $comment) {
                $data['comments'][$comment->id]['id'] = $comment->id;
                $data['comments'][$comment->id]['user_id'] = $comment->user_id;
                $data['comments'][$comment->id]['created_at'] = $comment->created_at->timestamp;
                $data['comments'][$comment->id]['comment_content'] = $comment->comment_content;
                $data['comments'][$comment->id]['reply_to_user_id'] = $comment->reply_to_user_id;
            };
            $datas[] = $data;
        };

        return response()->json([
                'status'  => true,
                'code'    => 0,
                'message' => '动态列表获取成功',
                'data' => $datas
            ])->setStatusCode(200);
    }

    /**
     * 创建动态
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if(!$request->storage_task_ids && !$request->feed_content) {
            return response()->json([
                'status'  => false,
                'code'    => 6001,
                'message' => '动态内容不能为空'
            ])->setStatusCode(400);
        }
        $storages = [];
        if($request->storage_task_ids) {
            $storage_task_ids = $request->storage_task_ids;
            $storageTasks = StorageTask::whereIn('id', $storage_task_ids)
                ->with('storage')
                ->get();
            $storages = $storageTasks->map(function ($storageTask) {
                return $storageTask->storage->id;
            });
        }
        $feed = new Feed();
        $feed->feed_content = $request->feed_content;
        $feed->feed_client_id = $request->getClientIp();
        $feed->user_id = $request->user()->id;
        $feed->feed_from = $request->feed_from;
        $feed->feed_latitude = $request->input('latitude', '');
        $feed->feed_longtitude = $request->input('longtitude', '');
        $feed->feed_geohash = $request->input('geohash', '');
        $feed->feed_title = $request->input('feed_title', '');
        $feed->feed_mark = $request->input('feed_mark', ($user->id.Carbon::now()->timestamp)*1000);//默认uid+毫秒时间戳
        $feed->save();
        $feed->storages()->sync($storages);

        return response()->json([
                'status' => true,
                'code' => 0,
                'message' => '动态创建成功'
            ])->setStatusCode(201);
    }

    public function read($feed_id)
    {
        if(!$feed_id) {
            return response()->json([
                'status' => false,
                'code' => 6003,
                'message' => '动态ID不能为空'
            ])->setStatusCode(400);
        }
        $feed = Feed::where('id',intval($feed_id))
            ->with([
                'diggs' => function($query) {
                    $query->take(8);
                },
                'storages'
            ])
            ->first();
        if(!$feed) {
           return response()->json([
                'status' => false,
                'code' => 6004,
                'message' => '动态不存在或已被删除'
            ])->setStatusCode(404); 
        }
        // 组装数据
        $data = [];
        // 用户标识
        $data['user_id'] = $feed->user_id;
        // 动态内容
        $data['feed'] = [];
        $data['feed']['id'] = $feed->id;
        $data['feed']['title'] = $feed->feed_title;
        $data['feed']['content'] = $feed->feed_content;
        $data['feed']['created_at'] = $feed->created_at->timestamp;
        $data['feed']['feed_from'] = $feed->feed_from;
        $data['feed']['feed_storages'] = $feed->storages->map(function($storage) {
            return [ 'storage_id' => $storage->id, 'width' => $storage->image_width, 'height' => $storage->image_height];
        });
        // 工具栏数据
        $data['tool'] = [];
        $data['tool']['digg'] = $feed->feed_digg_count;
        $data['tool']['view'] = $feed->feed_view_count;
        $data['tool']['comment'] = $feed->feed_comment_count;
        // 动态评论,详情默认为空，自动获取评论列表接口
        $data['comments'] = [];
        // 动态最新8条点赞的用户id
        $data['diggs'] = $feed->diggs->map(function($digg) {
            return $digg->user_id;
        });

        Feed::byFeedId($feed_id)->increment('feed_view_count');
        return response()->json([
                'status' => true,
                'code' => 0,
                'message' => '获取动态成功',
                'data' => $data
            ])->setStatusCode(200); 
    }

    /**
     * 最新动态列表构建
     * 
     * @author bs<414606094@qq.com>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getNewFeeds(Request $request)
    {
        $user_id  = Auth::guard('api')->user()->id ?? 0;
        // 设置单页数量
        $limit = $request->limit ?? 15;
        $feeds = Feed::orderBy('id', 'DESC')
            ->where(function($query) use ($request) {
                if($request->max_id > 0){
                    $query->where('id', '<', $request->max_id);
                }
            })
            ->withCount(['diggs' => function($query) use ($user_id) {
                if($user_id) {
                    $query->where('user_id', $user_id);
                }
            }])
            ->with('storages')
            ->take($limit)
            ->get();

        return $this->index($feeds, $user_id);
    }

    /**
     * 我关注的动态列表构建
     * 
     * @author bs<414606094@qq.com>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getFollowFeeds(Request $request)
    {
        $user_id  = Auth::guard('api')->user()->id;
        // 设置单页数量
        $limit = $request->limit ?? 15;
        $feeds = Feed::orderBy('id', 'DESC')
            ->whereIn('user_id', $request->user()->follows->pluck('following_user_id'))
            ->where(function($query) use ($request) {
                if($request->max_id > 0){
                    $query->where('id', '<', $request->max_id);
                }
            })
            ->withCount(['diggs' => function($query) use ($user_id) {
                if($user_id) {
                    $query->where('user_id', $user_id);
                }
            }])
            ->with('storages')
            ->take($limit)
            ->get();
        return $this->index($feeds, $user_id);
    }


    /**
     * 热门动态列表构建
     * 
     * @author bs<414606094@qq.com>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getHotFeeds(Request $request)
    {
        $user_id  = Auth::guard('api')->user()->id ?? 0;
        // 设置单页数量
        $limit = $request->limit ?? 15;
        $page = $request->page ?? 1;
        $skip = ($page - 1)*$limit;
        
        $feeds = Feed::orderBy('id', 'DESC')
            ->whereIn('id', FeedDigg::groupBy('feed_id')
                ->limit($limit)
                ->select('feed_id',DB::raw('COUNT(user_id) as diggcount'))
                ->where('created_at', '>', Carbon::now()->subMonth()->toDateTimeString())
                ->orderBy('diggcount', 'desc')
                ->skip($skip)
                ->pluck('feed_id')
                )
            ->withCount(['diggs' => function($query) use ($user_id) {
                if($user_id) {
                    $query->where('user_id', $user_id);
                }
            }])
            ->with('storages')
            ->get();

        return $this->index($feeds, $user_id);
    }
}
