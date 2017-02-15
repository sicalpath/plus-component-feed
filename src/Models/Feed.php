<?php
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models;

use Zhiyi\Plus\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    protected $table = 'feeds';

    protected $fillable = [
    	'feed_title',
    	'feed_content',
    	'feed_from',
    	'feed_latitude',
    	'feed_longtitude',
    	'feed_client_id',
    	'feed_goehash',
        'user_id'
    ];

    protected $hidden = [
    	'feed_client_id'
    ];

    /**
     * 单条动态属于一个用户
     * @return [type] [description]
     */
    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    /**
     * 动态拥有多条点赞记录
     * @return [type] [description]
     */
    public function diggs()
    {
    	return $this->hasMany(FeedDigg::class, 'feed_id');
    }


    /**
     *动态拥有多条评论
     * @return [type] [description]
     */
    public function comments()
    {
    	return $this->hasMany(FeedComment::class, 'feed_id');
    }

    /**
     * 一条动态可能会@多个用户
     * @return [type] [description]
     */
    public function atmes()
    {
    	return $this->hasMany(FeedAtme::class, 'feed_id');
    }

    /**
     * 动态会有多附件信息
     * @return [type] [description]
     */
    public function storages()
    {
    	return $this->hasMany(FeedStorage::class, 'feed_id');
    }

    /**
     * [scopeByUserId 通过用户ID查找相关动态]
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
     * @param  integer $feedId [description]
     * @return [type]          [description]
     */
    public function scopeByFeedId(Builder $query, int $feedId): Builder
    {
        return $query->where('feed_id', $feedId);
    }
}

