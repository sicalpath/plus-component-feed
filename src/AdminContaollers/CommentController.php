<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\AdminContaollers;

use Illuminate\Http\Request;
use Zhiyi\Plus\Http\Controllers\Controller;
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
        if (! $comment->delete()) {
            $comment->feed()->decrement('feed_comment_count');
            return response()->json()->setStatusCode(500);
        }

        return response(null, 204);
    }
}
