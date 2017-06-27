<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Observers\Fetch;

use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment;
use Zhiyi\Plus\Contracts\Model\FetchComment as CommentFetchConyract;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Repository\Feed as FeedRepository;

class CommentFetch implements CommentFetchConyract
{
    /**
     * Feed comment model.
     *
     * @var \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment
     */
    protected $comment;

    /**
     * The comment feed.
     *
     * @var \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed
     */
    protected $feed;

    /**
     * Create the comment fetch instance.
     *
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment $comment
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function __construct(FeedComment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment centent.
     *
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function getCommentContentAttribute(): string
    {
        return $this->comment->comment_content;
    }

    /**
     * Get target source display title.
     *
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function getTargetTitleAttribute(): string
    {
        return str_limit($this->feed()->feed_content ?? '', 100);
    }

    /**
     * Get target source image file with ID.
     *
     * @return int
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function getTargetImageAttribute(): int
    {
        if (! isset($this->feed()->images) || ! $this->feed()->images) {
            return 0;
        }

        foreach ((array) $this->feed()->images as $fileWith) {
            return $fileWith->id ?? 0;
        }
    }

    /**
     * Get target source id.
     *
     * @return int
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function getTargetIdAttribute(): int
    {
        return $this->feed()->id ?? 0;
    }

    /**
     * Get the comment to feed.
     *
     * @return [type]
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function feed()
    {
        if (! $this->feed instanceof Feed) {
            $repository = app(FeedRepository::class);
            $this->feed = $repository->find(
                $this->comment->feed_id
            );
            $repository->images();
        }

        return $this->feed;
    }
}
