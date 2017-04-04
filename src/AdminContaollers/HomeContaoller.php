<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\AdminContaollers;

use Zhiyi\Plus\Http\Controllers\Controller;
use function Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\view;

class HomeContaoller extends Controller
{
    /**
     * 分享管理后台入口.
     *
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function show()
    {
        return view('admin', [
            'base_url' => route('feed:admin'),
            'csrf_token' => csrf_token(),
        ]);
    }
}
