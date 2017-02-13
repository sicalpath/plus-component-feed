<?php
namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FeedAtme extends Model
{
    protected $table = 'feed_atmes';

    /**
     * 获取被@用户的基本信息
     * 
     * @author bs<414606094@qq.com>
     * @return object
     */
    public function atuser()
    {
        return $this->hasOne(User::class, 'at_user_id', 'id');
    }

    /**
     * 获取主动@用户的基本信息
     * 
     * @author bs<414606094@qq.com>
     * @return object
     */
    public function user()
    {
    	return $this->hasOne(User::class, 'user_id', 'id');
    }

    /**
     * 获取@的分享内容
     * 
     * @author bs<414606094@qq.com>
     * @return object
     */
    public function feed()
    {
    	return $this->belongsTo(Feed::class, 'feed_id', 'feed_id'); 
    }
}