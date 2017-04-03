<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models;

use Zhiyi\Plus\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FeedDigg extends Model
{
    protected $table = 'feed_diggs';

    protected $fillable = ['user_id', 'feed_id'];

    /**
     * 赞分享的用户信息.
     *
     * @author bs<414606094@qq.com>
     * @return object
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 被赞的分享信息.
     *
     * @author bs<414606094@qq.com>
     * @return object
     */
    public function feed()
    {
        return $this->belongsTo(Feed::class, 'feed_id', 'id');
    }

    /**
     * 根据被@动态id查找.
     * @author bs<414606094@qq.com>
     * @param  Builder $query  [description]
     * @param  int     $feedId [description]
     * @return [type]          [description]
     */
    public function scopeByFeedId(Builder $query, int $feedId): Builder
    {
        return $query->where('feed_id', $feedId);
    }

    /**
     * 根据被@用户id查找.
     *
     * @author bs<414606094@qq.com>
     * @param  Builder $query    [description]
     * @param  int $userId [description]
     * @return [type]            [description]
     */
    public function scopeByUserId(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
