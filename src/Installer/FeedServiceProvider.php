<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Installer;

use Zhiyi\Plus\Support\PackageHandler;
use Illuminate\Support\ServiceProvider;
use Zhiyi\Plus\Support\ManageRepository;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Installer\FeedPackageHandler;
use function Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\asset;
use function Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\base_path as component_base_path;

class FeedServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(
            component_base_path('/src/router.php')
        ); // 路由注入

        $this->publishes([
            component_base_path('assets') => $this->app->PublicPath().'/zhiyicx/plus-component-feed',
        ]); // 静态资源

        PackageHandler::loadHandleFrom('feed', FeedPackageHandler::class); // 注入安装处理器
    }

    public function register()
    {
        $this->app->make(ManageRepository::class)
        ->loadManageFrom('动态分享', 'feed:admin', [
            'route' => true,
            'icon'  => asset('feed-icon.png'),
        ]); // 后台地址
    }
}
