<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\API2;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Plus\Models\Comment as CommentModel;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed as FeedModel;
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
        $this->middleware('auth:api')->except(['index']);
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

    public function show()
    {
        // TODO.
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
    public function store(CommentFormRequest $request, ResponseContract $response, FeedModel $feed)
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

        // 共用评论标记
        $globalComment = new CommentModel();
        $globalComment->user_id = $comment->user_id;
        $globalComment->target_user = $comment->to_user_id;
        $globalComment->reply_user = $comment->reply_to_user_id;
        $globalComment->channel = 'feed';

        // 保存本地评论
        if (! $feed->comments()->save($comment) || ! $comment->id) {
            return $response->json(['message' => ['评论失败']])->setStatusCode(500);
        }

        // 保存公用标记
        $globalComment->target = $comment->id;
        $globalComment->saveOrFail();

        return $response->json([
            'message' => '评论成功',
            'id' => $comment->id,
        ])->setStatusCode(201);
    }

    public function destroy()
    {
        // TODO
    }
}