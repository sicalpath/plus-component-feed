<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Controllers\V2;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Zhiyi\Plus\Jobs\PushMessage;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment;

class FeedCommentController extends Controller
{
    /**
     * 查看一条分享的评论列表.
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request
     * @param  Feed    $feed_id
     */
    public function getList(Request $request, Feed $feed)
    {
        $limit = $request->input('limit', 15);
        $max_id = intval($request->input('max_id'));

        $datas = $feed->comments()->where(function ($query) use ($max_id) {
            if ($max_id > 0) {
                $query->where('id', '<', $max_id);
            }
        })
            ->select(['id', 'created_at', 'comment_content', 'user_id', 'to_user_id', 'reply_to_user_id', 'comment_mark'])
            ->orderBy('id', 'desc')
            ->take($limit)
            ->get();

        return response()->json($datas)->setStatusCode(200);
    }

    /**
     * 对一条动态或评论进行评论.
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request [description]
     */
    public function add(Request $request, Feed $feed)
    {
        $user_id = $request->user()->id;

        if (! $request->has('comment_content')) {
            return response()->json(['comment_content' => ['评论内容不能为空']])->setStatusCode(400);
        }

        $feedComment = new FeedComment();
        $feedComment->user_id = $user_id;
        $feedComment->feed_id = $feed->id;
        $feedComment->to_user_id = $feed->user_id;
        $feedComment->reply_to_user_id = $request->input('reply_to_user_id') ?? 0;
        $feedComment->comment_content = $request->input('comment_content');
        $feedComment->comment_mark = $request->input('comment_mark', ($user_id.Carbon::now()->timestamp) * 1000); //默认uid+毫秒时间戳

        if ($existComment = $feed->comments->where('user_id', $user_id)->where('comment_mark', $feedComment->comment_mark)->first()) {
            // 根据用户及移动端标记进行查重 以防移动端重复调用
            return response()->json($existComment->id)->setStatusCode(201);
        }

        DB::transaction(function () use ($feedComment, $feed) {
            $feedComment->save();
            $feed->increment('feed_comment_count'); //增加评论数量
        });

        if (($feedComment->reply_to_user_id == 0 && $feedComment->to_user_id != $feedComment->user_id) || ($feedComment->reply_to_user_id > 0 && $feedComment->reply_to_user_id != $feedComment->user_id)) {
            $extras = ['action' => 'comment', 'type' => 'feed', 'uid' => $user_id, 'feed_id' => $feed->id, 'comment_id' => $comment->id];
            $alert = '有人评论了你，去看看吧';
            $alias = $request->input('reply_to_user_id') > 0 ?: $feed->user_id;

            dispatch(new PushMessage($alert, (string) $alias, $extras));
        }

        return response()->json($feedComment->id)->setStatusCode(201);
    }

    /**
     * 删除一条评论.
     *
     * @author bs<414606094@qq.com>
     * @param  Request        $request
     * @param  FeedComment    $comment
     */
    public function delete(Request $request, FeedComment $comment)
    {
        $uid = $request->user()->id;

        if ($comment && $uid == $comment->user_id) {
            DB::transaction(function () use ($comment) {
                $comment->feed()->decrement('feed_comment_count'); // 减少评论数量
                $comment->delete();
            });
        }

        return response()->json()->setStatusCode(204);
    }

    /**
     * 根据id或当前用户的评论.
     *
     * @author bs<414606094@qq.com>
     * @return [type] [description]
     */
    public function search(Request $request)
    {
        $user_id = $request->user()->id;
        $comment_ids = $request->input('comment_ids');
        is_string($comment_ids) && $comment_ids = explode(',', $comment_ids);
        $limit = $request->input('limit', 15);
        $max_id = intval($request->input('max_id'));

        $comments = FeedComment::where(function ($query) use ($max_id) {
            if ($max_id > 0) {
                $query->where('id', '<', $max_id);
            }
        })
        ->where(function ($query) use ($comment_ids, $user_id) {
            if (count($comment_ids) > 0) {
                $query->whereIn('id', $comment_ids);
            } else {
                $query->where(function ($query) use ($user_id) {
                    $query->where('to_user_id', $user_id)->orwhere('reply_to_user_id', $user_id);
                });
            }
        })
        ->take($limit)->with(['feed' => function ($query) {
            $query->select(['id', 'created_at', 'user_id', 'feed_content', 'feed_title'])->with(['storages' => function ($query) {
                $query->select(['feed_storage_id']);
            }]);
        }])->get();

        return response()->json($comments)->setStatusCode(200);
    }
}
