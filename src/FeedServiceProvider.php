<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed;

use Zhiyi\Plus\Models\Comment;
use Zhiyi\Plus\Support\PackageHandler;
use Illuminate\Support\ServiceProvider;
use Zhiyi\Plus\Support\ManageRepository;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedComment;

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
        $this->registerObserves();

        // Load views.
        $this->loadViewsFrom(dirname(__DIR__).'/views/', 'feed:view');

        // Register migration files.
        $this->loadMigrationsFrom([
            dirname(__DIR__).'/database/migrations',
        ]);

        $this->publishes([
            dirname(__DIR__).'/assets' => $this->app->PublicPath().'/zhiyicx/plus-component-feed',
        ], 'public');
    }

    /**
     * register provided to provider.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function register()
    {
        $this->app->make(ManageRepository::class)->loadManageFrom('动态分享', 'feed:admin', [
            'route' => true,
            'icon' => asset('zhiyicx/plus-component-feed/feed-icon.png'),
        ]);
    }

    /**
     * Register model events.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function registerObserves()
    {
        Comment::observe(Observers\PlusCommentObserver::class);
        FeedComment::observe(Observers\CommentObserver::class);
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
