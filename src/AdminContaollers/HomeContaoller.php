<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\AdminContaollers;

use Zhiyi\Plus\Http\Controllers\Contaoller;
use function Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\view;
use function Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\base_path;

class HomeContaoller extends Contaoller
{
    /**
     * 分享管理后台入口.
     *
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function show()
    {
        return view('admin');
    }
}
