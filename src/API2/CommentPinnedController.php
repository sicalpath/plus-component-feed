<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\API2;

use Illuminate\Http\Request;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedPinned as FeedPinnedModel;

class CommentPinnedController extends Controller
{
    /**
     * 获取动态评论当前用户审核列表.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedPinned $model
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(Request $request, FeedPinnedModel $model)
    {
        $user = $request->user();
        $limit = $request->query('limit');
        $pinneds = $model->where('channel', 'comment')
            ->where('target_user', $user->id)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        $pinneds->load(['comment', 'comment.feed', 'comment.feed']);

        $pinneds = $pinneds->map(function (FeedPinnedModel $pinned) {
            // 基础信息
            $item = [
                'id' => $pinned->id,
                'amount' => $pinned->amount,
                'day' => $pinned->day,
                'user_id' => $pinned->user_id,
                'expires_at' => $pinned->expires_at,
                'created_at' => $pinned->created_at->toDateTimeString(),
            ];

            // 评论信息
            $item['comment'] = ! ($comment = $pinned->comment) ? null : [
                'id' => $comment->id,
                'content' => $comment->comment_content,
                'pinned' => boolval($comment->pinned),
                'user_id' => $comment->user_id,
                'reply_to_user_id' => $comment->reply_to_user_id,
                'created_at' => $comment->created_at->toDateTimeString(),
            ];

            // 动态信息
            $feed = $comment->feed ?? null;
            $item['feed'] = ! $feed ? null : [
                'id' => $feed->id,
                'content' => str_limit($feed->feed_content, 50, '...'),
            ];

            return $item;
        });

        return response()->json($pinneds, 200);
    }
}
