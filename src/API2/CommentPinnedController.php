<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\API2;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Plus\Models\WalletCharge as WalletChargeModel;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed as FeedModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedPinned as FeedPinnedModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment as FeedCommentModel;

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

    /**
     * 固定评论.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Carbon\Carbon $dateTime
     * @param \Zhiyi\Plus\Models\WalletCharge $charge
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment $comment
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedPinned $pinned
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function pass(Request $request,
                         ResponseContract $response,
                         Carbon $dateTime,
                         WalletChargeModel $charge,
                         FeedModel $feed,
                         FeedCommentModel $comment,
                         FeedPinnedModel $pinned)
    {
        $user = $request->user();

        if ($user->id !== $feed->user_id) {
            return $response->json(['message' => ['你没有权限操作']], 403);
        } elseif ($pinned->expires_at && $comment->pinned) {
            return $response->json(['message' => ['已被置顶，请勿重复发起']], 422);
        }

        $pinned->expires_at = $dateTime->addDay($pinned->day);
        $comment->pinned = 1;
        $comment->pinned_amount = $pinned->amount;

        // 动态发起人增加收款凭据
        $charge->user_id = $user->id;
        $charge->channel = 'user';
        $charge->account = $pinned->user_id;
        $charge->action = 1;
        $charge->amount = $pinned->amount;
        $charge->subject = sprintf('置顶评论《%s》', str_limit($comment->comment_content, 100, '...'));
        $charge->body = $charge->subject;
        $charge->status = 1;

        return $feed->getConnection()->transaction(function () use ($response, $pinned, $comment, $user, $charge) {
            $pinned->save();
            $comment->save();
            $user->wallet()->increment('balance', $charge->amount);
            $user->walletCharges()->save($charge);

            return $response->json(['message' => ['置顶成功']], 201);
        });
    }

    public function reject()
    {
        // doto.
    }

    public function delete()
    {
        // todo.
    }
}
