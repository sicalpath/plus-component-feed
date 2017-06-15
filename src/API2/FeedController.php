<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\API2;

use Illuminate\Http\Request;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Plus\Models\PayPublish as PayPublishModel;
use Zhiyi\Plus\Models\StorageTask as StorageTaskModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed as FeedModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Services\FeedCount as FeedCountService;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\FormRequest\API2\StoreFeedPost as StoreFeedPostRequest;

class FeedController extends Controller
{
    /**
     * 储存分享.
     *
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\FormRequest\API2\StoreFeedPost $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function store(StoreFeedPostRequest $request)
    {
        $user = $request->user();
        $feed = $this->fillFeedBaseData($request, new FeedModel());
        $storages = $this->queryFeedStorages($request);
        $payPublishs = $this->makePayPublishs($request, $storages);

        $feed->user_id = $user->id;
        $feed->save();

        try {
            $feed->getConnection()->transaction(function () use ($feed, $request, $payPublishs, $storages, $user) {
                $this->storeFeedPay($request, $feed); // 分享付费发布
                $this->storeFeedStoragePay($payPublishs, $feed); // 分享附件付费
                $feed->storages()->sync(array_values($storages)); // 分享附加附件关联
                StorageTaskModel::whereIn('id', array_keys($storages))->delete(); // 删除任务.
                $user->storages()->attach(array_values($storages)); // 附加用户附件关系.
                app(FeedCountService::class)->count($user->id, 'feeds_count', 'increment', 1); // 增加用户分享数量.
            });
        } catch (\Exception $e) {
            $feed->delete();

            throw $e;
        }

        return response()->json(['message' => ['发布成功']])->setStatusCode(201);
    }

    protected function storeFeedStoragePay(array $pays, FeedModel $feed)
    {
        foreach ($pays as $storage => $pay) {
            $pay->index = sprintf('storage:%s', $storage);
            $pay->subject = '购买动态附件';
            $pay->body = sprintf('购买动态《%s》的图片:%s', str_limit($feed->feed_title ?: $feed->feed_content, 100, '...'), $storage);
            $pay->save();
        }
    }

    protected function storeFeedPay(Request $request, FeedModel $feed)
    {
        $amount = $request->input('amount');

        if ($amount === null) {
            return;
        }

        $pay = new PayPublishModel();
        $pay->amount = $amount;
        $pay->index = sprintf('feed:%d', $feed->id);
        $pay->subject = sprintf('购买动态《%s》', str_limit($feed->feed_title ?: $feed->feed_content, 100, '...'));
        $pay->body = $pay->subject;
        $pay->save();
    }

    protected function makePayPublishs(Request $request, array $storages): array
    {
        return collect($request->input('storage_task'))->filter(function ($item) {
            return isset($item['amount']);
        })->mapWithKeys(function ($item) use ($storages) {
            return [$storages[$item['id']] => $item['amount']];
        })->map(function ($amount) {
            $pay = new PayPublishModel();
            $pay->amount = $amount;

            return $pay;
        })->all();
    }

    /**
     * 查询储存任务对应的储存IDs.
     * <pre>
     *    [3 => 12]; // key task_id, value storage_id
     * </pre>.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function queryFeedStorages(Request $request): array
    {
        $tasks = collect($request->input('storage_task'))->mapWithKeys(function ($item, $key) {
            return [$key => $item['id']];
        })->filter()->values();

        if (empty($tasks)) {
            return [];
        }

        return StorageTaskModel::with(['storage' => function ($query) {
            $query->select(['id', 'hash']);
        }])->whereIn('id', $tasks)->select(['id', 'hash'])->get()->mapWithKeys(function ($task) {
            return [$task->id => $task->storage->id];
        })->filter()->all();
    }

    /**
     * 填充分享初始数据.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed $feed
     * @return \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function fillFeedBaseData(Request $request, FeedModel $feed): FeedModel
    {
        foreach ($request->only(['feed_title', 'feed_content', 'feed_from', 'feed_mark', 'feed_latitude', 'feed_longtitude', 'feed_geohash']) as $key => $value) {
            $feed->$key = $value;
        }

        $feed->feed_client_id = $request->ip();
        $feed->audit_status = 1;

        return $feed;
    }
}
