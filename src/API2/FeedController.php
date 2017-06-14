<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\API2;

use Illuminate\Http\Request;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Plus\Models\StorageTask as StorageTaskModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed as FeedModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\FormRequest\API2\StoreFeedPost as StoreFeedPostRequest;

class FeedController extends Controller
{
    public function store(StoreFeedPostRequest $request)
    {
        $this->fillFeedBaseData($request, $feed = new FeedModel());
        $this->fillFeedStorages($request, $feed);

        // dd($feed);
    }

    protected function fillFeedStorages(Request $request, $feed)
    {
        
        $demo = StorageTaskModel::with(['storage'])->whereIn('id', collect($request->input('storage_task'))->mapWithKeys(function ($item, $key) {
            return [$key => $item['id']];
        })->filter()->values())->get();

        dd($demo->toArray());
    }

    protected function fillFeedBaseData(Request $request, FeedModel $feed): FeedModel
    {
        foreach ($request->only(['feed_title', 'feed_content', 'feed_from', 'feed_mark', 'latitude', 'longtitude', 'geohash']) as $key => $value) {
            $feed->$key = $value;
        }

        return $feed;
    }
}
