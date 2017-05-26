<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers\V2;

use Illuminate\Http\Request;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedAtme;

class FeedAtmeController extends Controller
{
    /**
     * 获取@我的分享列表.
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function get(Request $request)
    {
        $user_id = $request->user()->id;
        $limit = $request->get('limit', 15);

        $list = FeedAtme::ByAtUserId($user_id)->take($limit)->where(function ($query) use ($request) {
            if (intval($request->max_id) > 0) {
                $query->where('id', '<', intval($request->max_id));
            }
        })->with('feed')->orderBy('id', 'desc')->get();

        return response()->json($list)->setStatusCode(200);
    }
}
