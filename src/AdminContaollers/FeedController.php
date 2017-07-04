<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\AdminContaollers;

use Illuminate\Http\Request;
use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;

class FeedController extends Controller
{
    /**
     * Get feeds.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $model
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(Request $request, Feed $model)
    {
        $limit = (int) $request->query('limit', 20);

        $feeds = $model->with([
                'user',
                'paidNode',
                'images',
                'images.paidNode',
            ])
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($feeds)->setStatusCode(200);
    }

    /**
     * Delete feed.
     *
     * @param \Illuminate\Contracts\Cache\Repository $cache
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function destroy(CacheContract $cache, Feed $feed)
    {
        $feed->delete();
        $cache->forget(sprintf('feed:%s', $feed->id));

        return response(null, 204);
    }
}
