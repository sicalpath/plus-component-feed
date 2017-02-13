<?php
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers;

use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedAtme;

class FeedAtmeApiController extends Controller
{
	/**
	 * 获取@我的分享列表
	 *  
	 * @author bs<414606094@qq.com>
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function getAtmeList(Request $request)
	{
		$user = $request->attributes->get('user');
		$list = FeedAtme::byAtmeUserId($user)->feed()->get();

		if ($list) {

            return response()->json(static::createJsonData([
                'code'   => 0,
                'status' => true,
                'data' => $list->toArray(),
            ]))->setStatusCode(200);
		}

        return response()->json(static::createJsonData([
            'code'   => 0,
            'status' => true,
            'data' => [],
        ]))->setStatusCode(200);
	}
}