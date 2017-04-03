<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Installer;

use function Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\{
    asset
};
use Zhiyi\Component\Installer\PlusInstallPlugin\ComponentInfoInterface;

class Info implements ComponentInfoInterface
{
    /**
     * 应用名称.
     *
     * @author bs<414606094@qq.com>
     * @return string
     */
    public function getName(): string
    {
        return 'feed';
    }

    /**
     * 应用logo.
     *
     * @author bs<414606094@qq.com>
     * @return string
     */
    public function getLogo(): string
    {
        return asset('images/logo.png');
    }

    /**
     * 应用图标.
     *
     * @author bs<414606094@qq.com>
     * @return string
     */
    public function getIcon(): string
    {
        return asset('images/logo.png');
    }

    /**
     * 后台入口.
     *
     * @author bs<414606094@qq.com>
     * @return string
     */
    public function getAdminEntry()
    {
        return 'http://www.baidu.com';
    }
}
