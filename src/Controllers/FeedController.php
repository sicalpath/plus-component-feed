<?php
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers;

use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index()
    {
        return 'index';
    }

    /**
     * 创建动态
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function store(Request $request)
    {
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
        $feed['user_id'] = $request->user_id;
        // 判断动态来路,默认为来自pc
        $feed['feed_from'] = $request->feed_from ?? 1;
        $feed['latitude'] = $rqeuest->
    	dump($request->all());
    }
}