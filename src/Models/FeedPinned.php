<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models;

use Illuminate\Database\Eloquent\Model;

class FeedPinned extends Model
{
    /**
     *  Has feed.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function feed()
    {
        if ($this->channel !== 'feed') {
            return null;
        }

        return $this->hasOne(Feed::class, 'id', 'target');
    }

    /**
     * Has feed comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function comment()
    {
        if ($this->channel !== 'comment') {
            return null;
        }

        return $this->hasOne(FeedComment::class, 'id', 'target');
    }
}
