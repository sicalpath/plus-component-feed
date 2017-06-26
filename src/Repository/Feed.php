<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Repository;

use Carbon\Carbon;
use Zhiyi\Plus\Models\FileWith as FileWithModel;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed as FeedModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedDigg as FeedDiggModel;

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
            $this->model = $this->model->findOrFail($id, $columns);
            $this->model->load(['paidNode']);

            return $this->model;
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
        $this->model->setRelation('images', $this->cache->remember(sprintf('feed:%s:images', $this->model->id), $this->dateTime->copy()->addDays(7), function () {
            $this->model->load([
                'images',
                'images.paidNode',
            ]);

            return $this->model->images;
        }));

        return $this->model->images;
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
        $this->model->setRelation('diggs', $this->cache->remember(sprintf('feed:%s:info-diggs', $this->model->id), $minutes, function () {
            if (! $this->model->relationLoaded('diggs')) {
                $this->model->load(['diggs' => function ($query) {
                    $query->limit(8)
                        ->orderBy('id', 'desc');
                }]);
            }

            return $this->model->diggs;
        }));

        return $this->model->diggs;
    }

    /**
     * Has User digg.
     *
     * @param int $user
     * @return bool
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function hasDigg(int $user): bool
    {
        $cacheKey = sprintf('feed:%s:has-digg:%s', $this->model->id, $user);
        if ($this->cache->has($cacheKey)) {
            return $this->model->has_digg = $this->cache->get($cacheKey);
        }

        $this->model->has_digg = $this->model->diggs()->where('user_id', $user)->count() >= 1;
        $this->cache->forever($cacheKey, $this->model->has_digg);

        return $this->model->has_digg;
    }

    /**
     * Format feed data.
     *
     * @param int $user
     * @return Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function format(int $user = 0): FeedModel
    {
        $this->model->setRelation('images', $this->model->images->map(function (FileWithModel $item) use ($user) {
            $image = [
                'file' => $item->id,
                'size' => $item->size,
            ];
            if ($item->paidNode !== null) {
                $image['amount'] = $item->paidNode->amount;
                $image['type'] = $item->paidNode->extra;
                $image['paid'] = $item->paidNode->paid($user);
                $image['node'] = $item->paidNode->id;
            }

            return $image;
        }));

        $this->model->setRelation('diggs', $this->model->diggs->map(function (FeedDiggModel $item) {
            return $item['user_id'];
        }));

        if ($this->model->paidNode !== null) {
            $this->model->amount = $this->model->paidNode->amount;
            $this->model->paid = $this->model->paidNode->paid($user);
            $this->model->node = $this->model->paidNode->id;
        }

        unset($this->model->paidNode);

        return $this->model;
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
