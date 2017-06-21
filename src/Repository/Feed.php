<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Repository;

use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed as FeedModel;

class Feed
{
    protected $model;

    /**
     * Cache repository.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    protected $dateTime;

    /**
     * Create the cash type respositorie.
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function __construct(CacheContract $cache, FeedModel $model, Carbon $dateTime)
    {
        $this->cache = $cache;
        $this->model = $model;
        $this->dateTime = $dateTime;
    }

    /**
     * Find feed.
     *
     * @param int $id
     * @param array $columns
     * @return Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function find($id, $columns = ['*'])
    {
        return $this->model = $this->cache->remember(sprintf('feed:%s', $id), $this->dateTime->copy()->addDays(7), function () use ($id, $columns) {
            return $this->model->findOrFail($id, $columns);
        });
    }

    /**
     * Feed images.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function images()
    {
        return $this->model->images = $this->cache->remember(sprintf('feed:%s:images', $this->model->id), $this->dateTime->copy()->addDays(7), function () {
            return $this->model->images;
        });
    }

    /**
     * 获取详情页面点赞用户列表.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function infoDiggUsers()
    {
        $minutes = $this->dateTime->copy()->addDays(1);
        return $this->model->diggs = $this->cache->remember(sprintf('feed:%s:info-diggs', $this->model->id), $minutes, function () {
            if (! $this->model->relationLoaded('diggs')) {
                $this->model->load(['diggs' => function ($query) {
                    $query->limit(8)
                        ->orderBy('id', 'desc');
                }]);
            }
            
            return $this->model->diggs;
        });
    }

    /**
     * Has User digg.
     *
     * @param int $userId
     * @return boolean
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function hasDigg(int $userId): bool
    {
        $cacheKey = sprintf('feed:%s:has-digg:%s', $this->model->id, $userId);
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $value = $this->model->diggs()->where('user_id', $userId)->count() >= 1;
        $this->cache->forever($cacheKey, $value);

        return $value;
    }

    /**
     * Set feed model.
     *
     * @param Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $model
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function setModel(FeedModel $model)
    {
        $this->model = $model;

        return $this;
    }

    public function forget($key)
    {
        $this->cache->forget($key);
    }
}
