<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\API2;

use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\FormRequest\API2\StoreFeedPost as StoreFeedPostRequest;

class FeedController extends Controller
{
    public function store(StoreFeedPostRequest $request)
    {
        dd(
            $request->all()
        );
    }
}
