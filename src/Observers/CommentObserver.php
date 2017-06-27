<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Observers;

use Zhiyi\Plus\Models\Comment;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment;

class CommentObserver
{
    /**
     * Feed comment created.
     *
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment $feedComment
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function created(FeedComment $feedComment)
    {
        $comment = new Comment();
        $comment->user_id = $feedComment->user_id;
        $comment->target_user = $feedComment->to_user_id;
        $comment->reply_user = $feedComment->reply_to_user_id ?: 0;
        $comment->target = $feedComment->id;
        $comment->channel = 'feed';
        $comment->save();
    }

    /**
     * Feed comment deleted.
     *
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment $feedComment
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function deleted(FeedComment $feedComment)
    {
        Comment::where('channel', 'feed')
            ->where('target', $feedComment->id)
            ->delete();
    }
}
