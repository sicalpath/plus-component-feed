<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models;

use Zhiyi\Plus\Models\User;
use Zhiyi\Plus\Models\Storage;
use Illuminate\Database\Eloquent\Model;

class FeedStorage extends Model
{
    protected $table = 'feed_storages';
    protected $fillable = [
        'user_id',
        'feed_id',
        'feed_storage_id',
    ];

    public function feed()
    {
        return $this->belongsTo(Feed::class, 'id', 'feed_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    /**
     * 关联storages表.
     * @return [type] [description]
     */
    public function storage()
    {
        return $this->hasOne(Storage::class, 'id', 'feed_storage_id');
    }
}
