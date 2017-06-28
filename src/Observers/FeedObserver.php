<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Observers;

use Zhiyi\Plus\Models\Comment;
use Illuminate\Support\Facades\Cache;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;

class FeedObserver
{
    public function deleted(Feed $feed)
    {
        Cache::forget(sprintf('feed:%s', $feed->id));
    }
}
