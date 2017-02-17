<?php
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers;

use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Plus\Storages\Storage;

class FeedController extends Controller
{
    public function __construct(Request $request)
    {
        $request->setUserResolver(function() {
            return Auth::guard('api')->basic();
        });
    }

    public function index(Request $request)
    {   
        $user_id  = $request->user()->id ?? 0;
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
            ->with([
                'comments' => function ($query) {
                    $query->select('id', 'created_at', 'user_id', 'to_user_id', 'reply_to_user_id', 'comment_content')
                    ->take(3);
                }
            ])
            ->take($limit)
            ->get();
        dd($feeds->toArray());
        $datas = [];
        foreach($feeds as $feed) {
            $datas[$feed->id]['user_id'] = $feed->user_id;
            // 动态数据
            $datas[$feed->id]['feed'] = [];
            $datas[$feed->id]['feed']['feed_title'] = $feed->feed_title ?? '';
            $datas[$feed->id]['feed']['feed_content'] = $feed->feed_content;
            $datas[$feed->id]['feed']['created_at'] = $feed->created_at->timestamp;
            $datas[$feed->id]['feed']['feed_from'] = $feed->feed_from;
            // 工具数据
            $datas[$feed->id]['tool'] = [];
            $datas[$feed->id]['tool']['feed_view_count'] = $feed->feed_view_count;
            $datas[$feed->id]['tool']['feed_digg_count'] = $feed->feed_digg_count;
            $datas[$feed->id]['tool']['feed_comment_count'] = $feed->feed_comment_count;
            // 暂时剔除当前登录用户判定
            $datas[$feed->id]['tool']['is_digg_feed'] = $user_id ? $feed->diggs_count : 0;
            // 最新3条评论
            $datas[$feed->id]['comments'] = [];
            foreach($feed->comments()->orderBy('id', 'DESC')->take(3)->get() as $comment) {
                $datas[$feed->id]['comments'][$comment->id]['id'] = $comment->id;
                $datas[$feed->id]['comments'][$comment->id]['user_id'] = $comment->user_id;
                $datas[$feed->id]['comments'][$comment->id]['created_at'] = $comment->created_at->timestamp;
                $datas[$feed->id]['comments'][$comment->id]['comment_content'] = $comment->comment_content;
                $datas[$feed->id]['comments'][$comment->id]['reply_to_user_id'] = $comment->reply_to_user_id;
            };
        };

        return response()->json([
                'status'  => true,
                'code'    => 0,
                'message' => '动态列表获取成功',
                'data' => $datas
            ])->setStatusCode(201);
    }

    /**
     * 创建动态
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required',
            'user_id' => 'required'
        ]);
    	if(!$request->content) {
	        return response()->json([
	            'status'  => false,
	            'code'    => 6001,
	            'message' => '动态内容不能为空'
	        ])->setStatusCode(400);
    	}
        if(!$request->user_id) {
            return response()->json([
                'status' => false,
                'code' => 6002,
                'message' => '动态user_id不能为空'
            ])->setStatusCode(400);
        }
        $feed = [];
        $request->title && $feed['feed_title'] = $request->title;
        $feed['feed_content'] = $request->content;
        $feed['feed_client_id'] = $request->getClientIp();
        $feed['user_id'] = intval($request->user_id);
        // 判断动态来路,默认为来自pc
        $feed['feed_from'] = $request->feed_from ?? 1;
        $feed['latitude'] = $rqeuest->latitude ?? '';
        $feed['longtitude'] = $request->longtitude ?? '';
    	Feed::create($feed);

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
            return $storage->feed_storage_id;
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

        return response()->json([
                'status' => true,
                'code' => 0,
                'message' => '获取动态成功',
                'data' => $data
            ])->setStatusCode(201); 
    }
}