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
    	dump($request->all());
    }
}