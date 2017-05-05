<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\AdminContaollers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Traits\PaginatorPage;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment as Comment;

class CommentController extends Controller
{
    use PaginatorPage;

    /**
     * 获取评论列表.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function show(Request $request)
    {
        $limit = (int) $request->query('limit', 20);

        $query = app(Comment::class)->newQuery();
        $query->with(['user']);
        $paginator = $query->simplePaginate($limit);

        $data = [
            'comments' => $paginator->getCollection()->toArray(),
            'pervPage' => $this->getPrevPage($paginator),
            'nextPage' => $this->getNextPage($paginator),
        ];

        return response()->json($data)->setStatusCode(200);
    }

    /**
     * Delete comment.
     *
     * @param Comment $comment
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function delete(Comment $comment)
    {
        DB::beginTransaction();
        try {
            $feed = new Feed();
            $feed->where('id', $comment->feed_id)->decrement('feed_comment_count'); // 统计相关动态评论数量

            $comment->delete();
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
