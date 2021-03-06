<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models;

use Zhiyi\Plus\Models\User;
use Zhiyi\Plus\Models\FileWith;
use Zhiyi\Plus\Models\PaidNode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feed extends Model
{
    use SoftDeletes,
        Concerns\HasFeedCollect;

    /**
     * The model table name.
     *
     * @var string
     */
    protected $table = 'feeds';

    protected $fillable = [
        'feed_content',
        'feed_from',
        'feed_latitude',
        'feed_longtitude',
        'feed_client_id',
        'feed_goehash',
        'feed_mark',
        'user_id',
    ];

    protected $hidden = [
        'feed_client_id',
    ];

    /**
     * Has feed pinned.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function pinned()
    {
        return $this->hasOne(FeedPinned::class, 'target', 'id')
            ->where('channel', 'feed');
    }

    /**
     * Get feed images.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function images()
    {
        return $this->hasMany(FileWith::class, 'raw', 'id')
            ->where('channel', 'feed:image');
    }

    /**
     * 动态付费节点.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function paidNode()
    {
        return $this->hasOne(PaidNode::class, 'raw', 'id')
            ->where('channel', 'feed');
    }

    /**
     * 动态评论付费节点.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function commentPaidNode()
    {
        return $this->hasOne(PaidNode::class, 'raw', 'id')
            ->where('channel', 'feed:comment');
    }

    /**
     * 单条动态属于一个用户.
     * @return [type] [description]
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 动态拥有多条点赞记录.
     * @return [type] [description]
     */
    public function diggs()
    {
        return $this->hasMany(FeedDigg::class, 'feed_id');
    }

    /**
     *动态拥有多条评论.
     * @return [type] [description]
     */
    public function comments()
    {
        return $this->hasMany(FeedComment::class, 'feed_id', 'id');
    }

    /**
     * [scopeByUserId 通过用户ID查找相关动态].
     * @param  Builder $query [description]
     * @param  string  $phone [description]
     * @return [type]         [description]
     */
    public function scopeByUserId(Builder $query, integer $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * 通过动态id查找相关动态
     *
     * @author bs<414606094@qq.com>
     * @param  Builder $query  [description]
     * @param  int $feedId [description]
     * @return [type]          [description]
     */
    public function scopeByFeedId(Builder $query, int $feedId): Builder
    {
        return $query->where('id', $feedId);
    }

    /**
     * 筛选已审核动态
     *
     * @author bs<414606094@qq.com>
     * @param  Builder $query [description]
     * @return [type]         [description]
     */
    public function scopeByAudit(Builder $query): Builder
    {
        return $query->where('audit_status', 1);
    }

    /**
     * 动态拥有多条收藏记录.
     * @return [type] [description]
     */
    public function collection()
    {
        return $this->hasMany(FeedCollection::class, 'feed_id');
    }
}
