<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Installer;

use Closure;
use Zhiyi\Plus\Models\Comment;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Zhiyi\Component\Installer\PlusInstallPlugin\AbstractInstaller;
use function Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\base_path as component_base_path;

class Installer extends AbstractInstaller
{
    /**
     * Get the application info.
     *
     * @return void|\Zhiyi\Component\Installer\PlusInstallPlugin\ComponentInfoInterface
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function getComponentInfo()
    {
        return app(Info::class);
    }

    /**
     * register routers.
     * @return [type] [description]
     */
    public function router()
    {
        return dirname(__DIR__).'/router.php';
    }

    /**
     * component installer.
     * @param  Closure $next [description]
     * @return [type]        [description]
     */
    public function install(Closure $next)
    {
        if (! Schema::hasTable('feed_atmes')) {
            Schema::create('feed_atmes', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->comment('主键');
                $table->timestamps();
            });
            include component_base_path('/database/table_feed_atmes_columns.php');
        }

        if (! Schema::hasTable('feeds')) {
            Schema::create('feeds', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->comment('primary key');
                $table->timestamps();
                $table->softDeletes();
            });
            include component_base_path('/database/table_feeds_columns.php');
        }

        if (! Schema::hasTable('feed_diggs')) {
            Schema::create('feed_diggs', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->comment('primary key');
                $table->timestamps();
            });
            include component_base_path('/database/table_feed_diggs_columns.php');
        }

        if (! Schema::hasTable('feed_comments')) {
            Schema::create('feed_comments', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->comment('primary key');
                $table->timestamps();
                $table->softDeletes();
            });
            include component_base_path('/database/table_feed_comments_columns.php');
        }

        if (! Schema::hasTable('feed_storages')) {
            Schema::create('feed_storages', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->comment('primary key');
                $table->timestamps();
            });
            include component_base_path('/database/table_feed_storages_columns.php');
        }

        if (! Schema::hasTable('feed_collections')) {
            Schema::create('feed_collections', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->comment('primary key');
                $table->timestamps();
            });
            include component_base_path('/database/table_feed_collections_columns.php');
        }

        if (! Schema::hasTable('feed_views')) {
            Schema::create('feed_views', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->comment('primary key');
                $table->timestamps();
            });
            include component_base_path('/database/table_feed_views_columns.php');
        }

        $next();
    }

    /**
     * Do run update the compoent.
     *
     * @param Closure $next
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function update(Closure $next)
    {
        include component_base_path('/database/table_feeds_columns.php');
        include component_base_path('/database/table_feed_comments_columns.php');
        include component_base_path('/database/table_feed_diggs_columns.php');
        include component_base_path('/database/table_feed_comments_columns.php');
        include component_base_path('/database/table_feed_storages_columns.php');
        include component_base_path('/database/table_feed_collections_columns.php');
        $next();
    }

    /**
     * uninstall component.
     * @param  Closure $next [description]
     * @return [type]        [description]
     */
    public function uninstall(Closure $next)
    {
        Comment::where('component', 'feed')->delete();
        Schema::dropIfExists('feeds');
        Schema::dropIfExists('feed_atmes');
        Schema::dropIfExists('feed_diggs');
        Schema::dropIfExists('feed_comments');
        Schema::dropIfExists('feed_storages');
        Schema::dropIfExists('feed_collections');
        $next();
    }

    /**
     * setting static files.
     * @return [type] [description]
     */
    public function resource()
    {
        return component_base_path('/assets');
    }
}
