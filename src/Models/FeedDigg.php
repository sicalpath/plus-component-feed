<?php
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models;

use Zhiyi\Plus\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FeedDigg extends Model
{
    protected $table = 'feed_diggs';

    /**
     * 赞分享的用户信息
     * 
     * @author bs<414606094@qq.com>
     * @return object
     */
    public function user()
    {
    	return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * 被赞的分享信息
     * 
     * @author bs<414606094@qq.com>
     * @return object
     */
    public function feed()
    {
    	return $this->belongsTo(Feed::class, 'feed_id', 'feed_id');
    }
}