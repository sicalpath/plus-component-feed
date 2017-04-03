<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models;

use Zhiyi\Plus\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FeedAtme extends Model
{
    protected $table = 'feed_atmes';

    protected $primaryKey = 'id';

    protected $fillable = ['at_user_id', 'user_id', 'feed_id', 'created_at', 'updated_at'];

    /**
     * 获取被@用户的基本信息.
     *
     * @author bs<414606094@qq.com>
     * @return object
     */
    public function atuser()
    {
        return $this->belongsTo(User::class, 'at_user_id', 'id');
    }

    /**
     * 获取主动@用户的基本信息.
     *
     * @author bs<414606094@qq.com>
     * @return object
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 获取@的分享内容.
     *
     * @author bs<414606094@qq.com>
     * @return object
     */
    public function feed()
    {
        return $this->belongsTo(Feed::class, 'feed_id', 'id');
    }

    /**
     * 根据被@用户id查找.
     *
     * @author bs<414606094@qq.com>
     * @param  Builder $query    [description]
     * @param  int $atUserId [description]
     * @return [type]            [description]
     */
    public function scopeByAtUserId(Builder $query, int $atUserId): Builder
    {
        return $query->where('at_user_id', $atUserId);
    }

    /**
     * 根据被maxid翻页查询.
     *
     * @author bs<414606094@qq.com>
     * @param  Builder $query    [description]
     * @param  int $maxId [description]
     * @return [type]            [description]
     */
    public function scopeByMaxId(Builder $query, int $maxId): Builder
    {
        return $query->where('id', '<', $maxId);
    }
}
