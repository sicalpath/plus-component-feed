<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\AdminContaollers;

use DB;
use Zhiyi\Plus\Models\Digg;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedAtme;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedDigg;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Services\FeedCount;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Traits\PaginatorPage;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedCollection;

class FeedController extends Controller
{
    use PaginatorPage;

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

        $feeds = $model->with(['images', 'user'])
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

    /**
     * 审核动态状态.
     *
     * @param Request $request
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function reviewFeed(Request $request, Feed $feed)
    {
        $audit = (int) $request->input('audit', $feed->audit_status);

        if (! in_array($audit, [1, 2])) {
            return response()->json([
                'message' => '审核状态错误',
            ])->setStatusCode(422);
        }

        $feed->audit_status = $audit;

        if (! $feed->save()) {
            return response()->json([
                'message' => '操作失败',
            ])->setStatusCode(500);
        }

        return response()->json([
            'message' => '操作成功',
        ])->setStatusCode(201);
    }

    /**
     * 删除分享接口.
     *
     * @param Feed $feed
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function deleteFeed(Feed $feed)
    {
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
            $count->count($feed->user_id, 'feeds_count', 'decrement'); // 更新动态作者的动态数量
            $count->count($feed->user_id, 'diggs_count', 'decrement', $feed->feed_digg_count); // 更新动态作者收到的点赞数量

            $feed->delete();
        } catch (QueryException $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->formatMessage(),
            ])->setStatusCode(500);
        }
        DB::commit();

        return response(null, 204);
    }
}
