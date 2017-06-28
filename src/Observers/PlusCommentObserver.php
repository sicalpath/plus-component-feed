<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Observers;

use Zhiyi\Plus\Models\Comment;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment;

class PlusCommentObserver
{
    /**
     * Global Comment deleted.
     *
     * @param \Zhiyi\Plus\Models\Comment $comment
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function deleted(Comment $comment)
    {
        return $this->validateOr($comment, function (FeedComment $feedComment) {
            $feedComment->delete();
        });
    }

    /**
     * Fetch event.
     *
     * @param \Zhiyi\Plus\Models\Comment $comment
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function fetch(Comment $comment)
    {
        return $this->validateOr($comment, function (FeedComment $feedComment) {
            return new Fetch\CommentFetch($feedComment);
        });
    }

    /**
     * Validate or run call.
     *
     * @param \Zhiyi\Plus\Models\Comment $comment
     * @param callable $call
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function validateOr(Comment $comment, callable $call)
    {
        if ($comment->channel !== 'feed' || ! ($comment = FeedComment::find($comment->target))) {
            return null;
        }

        return call_user_func_array($call, [$comment]);
    }
}
