<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Concerns;

use Zhiyi\Plus\Models\User;
use Illuminate\Support\Facades\Cache;

trait HasFeedCollect
{
    /**
     * 动态收藏用户列表.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function collections()
    {
        return $this->belongsToMany(User::class, 'feed_collections', 'feed_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * 判断当前用户是否已经关注过.
     *
     * @param int $user
     * @return bool
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function collected(int $user): bool
    {
        $cacheKey = sprintf('feed-collected:%s,%s', $this->id, $user);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $status = $this->collections()->newPivotStatementForId($user)->first() !== null;
        Cache::forever($cacheKey, $status);

        return $status;
    }

    /**
     * 动态收藏.
     *
     * @param int $user
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function collect(int $user)
    {
        $this->forgetCollet($this->id, $user);

        return $this->collections()->attach($user);
    }

    /**
     * 取消动态收藏.
     *
     * @param int $user
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function uncollect(int $user)
    {
        $this->forgetCollet($this->id, $user);

        return $this->collections()->detach($user);
    }

    /**
     * 清理分享和用户关注缓存.
     *
     * @param int $feed
     * @param int $user
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function forgetCollet(int $feed, int $user)
    {
        $cacheKey = sprintf('feed-collected:%s,%s', $feed, $user);
        Cache::forget($cacheKey);
    }
}
