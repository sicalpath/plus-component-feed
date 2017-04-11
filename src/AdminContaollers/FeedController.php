<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\AdminContaollers;

use Illuminate\Http\Request;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;

class FeedController extends Controller
{
    /**
     * 显示所有feeds.
     *
     * @param Request $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function showFeeds(Request $request)
    {
        $limit = (int) $request->query('limit', 20);
        $showUser = (bool) $request->query('show_user', false);

        $query = app(Feed::class)->newQuery();

        if ($showUser) {
            $query->with('user');
        }

        $paginator = $query->simplePaginate($limit);

        $data = [
            'feeds' => $paginator->getCollection()->toArray(),
            'pervPage' => $this->getPrevPage($paginator),
            'nextPage' => $this->getNextPage($paginator),
        ];

        return response()->json($data)->setStatusCode(200);
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
        if (! $feed->delete()) {
            return response()->json([
                'message' => '删除失败',
            ])->setStatusCode(500);
        }

        return response(null, 204);
    }

    /**
     * 获取下一页页码.
     *
     * @param PaginatorContract $paginator
     * @return int|null|void
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function getNextPage(PaginatorContract $paginator)
    {
        if ($paginator->hasMorePages()) {
            return $paginator->currentPage() + 1;
        }
    }

    /**
     * 获取上一页的页码.
     *
     * @param PaginatorContract $paginator
     * @return int|null|void
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function getPrevPage(PaginatorContract $paginator)
    {
        if ($paginator->currentPage() > 1) {
            return $paginator->currentPage() - 1;
        }
    }
}
