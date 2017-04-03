<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models;

use DB;
use Zhiyi\Plus\Models\User;
use Zhiyi\Plus\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FeedComment extends Model
{
    protected $table = 'feed_comments';

    protected $fillable = [
        'user_id',
        'reply_to_user_id',
        'to_user_id',
        'feed_id',
        'comment_content',
    ];

    /**
     * 单条评论属于一个评论发起用户.
     * @return [type] [description]
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 单条评论属于一个评论发起用户.
     * @return [type] [description]
     */
    public function replyUser()
    {
        return $this->belongsTo(User::class, 'reply_to_user_id', 'id');
    }

    /**
     * 单条评论属于一条动态
     * @return [type] [description]
     */
    public function feed()
    {
        return $this->belongsTo(Feed::class, 'feed_id', 'id');
    }

    /**
     * 根据用户ID查找评论.
     * @param  Builder $query  [description]
     * @param  int $userId [description]
     * @return [type]          [description]
     */
    public function scopeByUserId(Builder $query, integer $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * 根据被评论者ID构建查询器.
     * @param  Builder $query    [description]
     * @param  int $toUserId [description]
     * @return [type]            [description]
     */
    public function scopeByToUserId(Builder $query, integer $toUserId)
    {
        return $query->where('to_user_id', $toUserId);
    }

    /**
     * 查找评论中的二级回复.
     * @param  Builder $query         [description]
     * @param  int $replyToUserId [description]
     * @return [type]                 [description]
     */
    public function scopeByReplyToUserId(Builder $query, integer $replyToUserId)
    {
        return $query->where('reply_to_user_id', $replyToUserId);
    }

    /**
     * 根据动态id查找评论.
     *
     * @author bs<414606094@qq.com>
     * @param  Builder $query  [description]
     * @param  int $feedId [description]
     * @return [type]          [description]
     */
    public function scopeByFeedId(Builder $query, int $feedId)
    {
        return $query->where('feed_id', $feedId);
    }

    /**
     * 覆盖删除方法  同步到tsplus评论表.
     *
     * @author bs<414606094@qq.com>
     * @param  array  $options [description]
     * @return [type]          [description]
     */
    public function save(array $options = [])
    {
        $comment = [
            'component' => 'feed',
            'user_id' => $this->user_id,
            'to_user_id' => $this->to_user_id,
            'reply_to_user_id' => $this->reply_to_user_id,
        ];

        DB::transaction(function () use ($comment) {
            parent::save();
            $comment['comment_id'] = $this->id;
            Comment::create($comment);
        });

        return $this;
    }

    /**
     * 同步删除.
     *
     * @author bs<414606094@qq.com>
     * @return [type] [description]
     */
    public function delete()
    {
        $map = [
            'comment_id' => $this->id,
            'component' => 'feed',
        ];

        DB::transaction(function () use ($map) {
            parent::delete();
            Comment::where($map)->delete();
        });

        return $this;
    }
}
