<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\AdminContaollers;

use Zhiyi\Plus\Http\Controllers\Controller;
use function Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\view;
use function Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\asset;

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
        $scripts = [
            asset('admin.js'),
        ];

        return view('admin', [
            'scripts' => $scripts,
        ]);
    }
}
