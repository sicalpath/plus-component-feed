<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\API2;

use Illuminate\Http\Request;
use Zhiyi\Plus\Jobs\PushMessage;
use Illuminate\Database\Eloquent\Builder;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Services\FeedCount;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed as FeedModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedDigg as FeedDiggModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Repository\Feed as FeedRepository;

class FeedDiggController extends Controller
{
    /**
     * Get feed diggs.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(Request $request, ResponseContract $response, FeedModel $feed)
    {
        $limit = $request->query('limit', 20);
        $after = $request->query('after');
        $diggs = $feed
            ->diggs()
            ->when((bool) $after, function (Builder $query) use ($after) {
                return $query->where('id', '<', $after);
            })
            ->limit($limit)
            ->get();

        return $response->json($diggs->map(function (FeedDiggModel $digg) {
            return $digg->user_id;
        }))->setStatusCode(200);
    }

    /**
     * 创建喜欢.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Repository\Feed $repository
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Services\FeedCount $count
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedDigg $digg
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function store(Request $request,
                          ResponseContract $response,
                          FeedRepository $repository,
                          FeedCount $count,
                          FeedDiggModel $digg,
                          FeedModel $feed)
    {
        $user = $request->user();
        $cacheKey = sprintf('feed:%s', $feed->id);

        if ($feed->diggs()->where('user_id', $user->id)->first()) {
            return $response->json(['message' => ['已经赞过了']])->setStatusCode(422);
        }

        // digg
        $digg->user_id = $user->id;

        $feed->getConnection()->transaction(function () use ($feed, $user, $digg, $count) {
            $feed->increment('feed_digg_count', 1);
            $feed->diggs()->save($digg);
            $count->count($user->id, 'diggs_count', 'increment');
        });

        $repository->forget($cacheKey);
        dispatch(new PushMessage('有人赞了你的动态，去看看吧', (string) $user->id, [
            'action' => 'digg',
        ]));

        return $response->json(['message' => ['成功']])->setStatusCode(201);
    }

    /**
     * 取消赞接口.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Repository\Feed $repository
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Services\FeedCount $count
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function destroy(Request $request,
                            ResponseContract $response,
                            FeedRepository $repository,
                            FeedCount $count,
                            FeedModel $feed)
    {
        $user = $request->user();
        $cacheKey = sprintf('feed:%s', $feed->id);

        $feed->getConnection()->transaction(function () use ($feed, $user, $count) {
            $feed->decrement('feed_digg_count', 1);
            $feed->diggs()->where('user_id', $user->id)->delete();
            $count->count($user->id, 'diggs_count', 'decrement');
        });
        $repository->forget($cacheKey);

        return $response->json(null, 204);
    }
}
