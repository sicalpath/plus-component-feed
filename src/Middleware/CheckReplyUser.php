<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Middleware;

use Closure;
use Illuminate\Http\Request;
use Zhiyi\Plus\Traits\CreateJsonResponseData;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Plus\Models\User;

class CheckReplyUser
{
    use CreateJsonResponseData;
    
    /**
     * 验证被回复者是否存在.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {   
        $uid = intval($request->input('reply_to_user_id'));
        if (!$uid) {
            return response()->json([
                'status' => false,
                'code' => 6008,
                'message' => '被回复者不能为空'
            ])->setStatusCode(400);
        }
        $user = User::find($uid);
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => 6009,
                'message' => '被回复者不存在或已删除'
            ])->setStatusCode(400);
        }

        return $next($request);
    }
}
