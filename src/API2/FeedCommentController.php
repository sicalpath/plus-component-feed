<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\API2;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed as FeedModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Repository\Feed as FeedRepository;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment as FeedCommentModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\FormRequest\API2\StoreFeedComment as CommentFormRequest;

class FeedCommentController extends Controller
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Create the contoller instance.
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function __construct(ApplicationContract $app)
    {
        $this->app = $app;
        $this->middleware('auth:api')->except(['index', 'show']);
    }

    /**
     * Feed comments.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $[name]
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(Request $request, ResponseContract $response, FeedModel $feed)
    {
        $limit = $request->query('limit', 20);
        $after = $request->query('after');

        $comments = $feed->comments()
            ->when((bool) $after, function (Builder $query) use ($after) {
                return $query->where('id', '<', $after);
            })
            ->where('pinned', 0)
            ->limit($limit)
            ->get();

        return $response->json([
            'comments' => $comments,
            'pinned' => $this->getPinnedComment($request, $feed),
        ])->setStatusCode(200);
    }

    /**
     * Get feed pinned comments.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function getPinnedComment(Request $request, FeedModel $feed)
    {
        if (! $request->query('after')) {
            return [];
        }

        return $feed->comments()
            ->where('pinned', 1)
            ->get()->all();
    }

    /**
     *  Get feed comment.
     *
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param int $feed
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment $comment
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function show(ResponseContract $response, int $feed, FeedCommentModel $comment)
    {
        unset($feed);

        return $response->json($comment)->setStatusCode(200);
    }

    /**
     * Store feed comment.
     *
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\FormRequest\API2\StoreFeedComment $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function store(CommentFormRequest $request, ResponseContract $response, FeedRepository $repository, FeedModel $feed)
    {
        $reply_to_user_id = $request->input('reply_to_user_id', 0);
        $comment_content = $request->input('comment_content');
        $comment_mark = $request->input('comment_mark');
        $user = $request->user();

        // 本地评论列
        $comment = new FeedCommentModel();
        $comment->user_id = $user->id;
        $comment->to_user_id = $feed->user_id;
        $comment->reply_to_user_id = $reply_to_user_id;
        $comment->comment_content = $comment_content;
        $comment->comment_mark = $comment_mark;
        $comment->pinned = 0;

        $feed->getConnection()->transaction(function () use ($feed, $comment) {
            $feed->comments()->save($comment);
            $feed->increment('feed_comment_count', 1);
        });

        $repository->forget(sprintf('feed:%s', $feed->id));

        return $response->json([
            'message' => '评论成功',
            'id' => $comment->id,
        ])->setStatusCode(201);
    }

    /**
     * Delete feed comment.
     *
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Repository\Feed $repository
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment $comment
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function destroy(ResponseContract $response, FeedRepository $repository, FeedModel $feed, FeedCommentModel $comment)
    {
        $feed->getConnection()->transaction(function () use ($feed, $comment) {
            $comment->delete();
            $feed->decrement('feed_comment_count', 1);
        });
        $repository->forget(sprintf('feed:%s', $feed->id));

        return $response->json(null, 204)
    }
}
