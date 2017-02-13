<?php
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FeedComment extends Model
{
    protected $table = 'feed_comments';

    protected $fillable = [
    	'user_id',
    	'reply_to_user_id',
    	'feed_id',
    	'comment_content'
    ];

    /**
     * 单条评论属于一个用户
     * @return [type] [description]
     */
    public function user()
    {
    	return $this->belongsTo(User::class, 'id', 'user_id');
    }

    /**
     * 单条评论属于一条动态
     * @return [type] [description]
     */
    public function feed()
    {
    	return $this->belongsTo(Feed::class, 'feed_id', 'feed_id');
    }

    /**
     * 根据用户ID查找评论
     * @param  Builder $query  [description]
     * @param  integer $userId [description]
     * @return [type]          [description]
     */
    public function scopeByUserId(Builder $query, integer $userId)
    {
    	return $query->where('user_id', $userId);
    }

}