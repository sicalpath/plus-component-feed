<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\API2;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed as FeedModel;

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

    public function store()
    {
        // TODO.
    }

    public function destroy()
    {
        // TODO
    }
}
