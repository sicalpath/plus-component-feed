<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed;

use Zhiyi\Plus\Support\PackageHandler;
use Illuminate\Support\ServiceProvider;

class FeedServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the provider.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function boot()
    {
        $this->routeMap();
        $this->publishHandler();

        // Load views.
        $this->loadViewsFrom(dirname(__DIR__).'/views/', 'feed:view');

        $this->publishes([
            dirname(__DIR__).'/assets' => $this->app->PublicPath().'/zhiyicx/plus-component-feed',
        ]);
    }

    /**
     * Publish handler.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function publishHandler()
    {
        PackageHandler::loadHandleFrom('feed', FeedPackageHandler::class);
    }

    /**
     * Register route.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function routeMap()
    {
        $this->app->make(RouteRegistrar::class)->all();
    }
}
