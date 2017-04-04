<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\AdminContaollers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment;
use function Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\view;

class HomeContaoller extends Controller
{
    /**
     * 分享管理后台入口.
     *
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function show()
    {
        return view('admin', [
            'base_url' => route('feed:admin'),
            'csrf_token' => csrf_token(),
        ]);
    }

    /**
     * 获取分享统计信息.
     *
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function statistics()
    {
        $expiresAt = Carbon::now()->addDay(1);

        $feedsCount = Cache::remember('feeds-count', $expiresAt, function () {
            return Feed::count();
        });

        $commentsCount = Cache::remember('feeds-comments-count', $expiresAt, function () {
            return FeedComment::count();
        });

        return response()->json([
            'feedsCount' => $feedsCount,
            'commentsCount' => $commentsCount,
        ])->setStatusCode(200);
    }
}
