<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed;

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

        // Load views.
        $this->loadViewsFrom(dirname(__DIR__).'/views/', 'feed:view');

        $this->publishes([
            dirname(__DIR__).'/assets' => $this->app->PublicPath().'/zhiyicx/plus-component-feed',
        ]);
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
