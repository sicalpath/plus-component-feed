<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Installer;

use Carbon\Carbon;
use Zhiyi\Plus\Models\Comment;
use Zhiyi\Plus\Models\Permission;
use Zhiyi\Plus\Support\PackageHandler;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use function Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\base_path as component_base_path;

class FeedPackageHandler extends PackageHandler
{
    public function removeHandle($command)
    {
        if ($command->confirm('This will delete your datas for feeds, continue?')) {
            Comment::where('component', 'feed')->delete();
            Permission::whereIn('name', ['feed-post', 'feed-comment', 'feed-digg', 'feed-collection'])->delete();
            Schema::dropIfExists('feeds');
            Schema::dropIfExists('feed_atmes');
            Schema::dropIfExists('feed_diggs');
            Schema::dropIfExists('feed_comments');
            Schema::dropIfExists('feed_storages');
            Schema::dropIfExists('feed_collections');

            $command->info('The Feed Component has been removed');
        }
    }

    public function installHandle($command)
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

        $time = Carbon::now();

        Permission::insert([
            [
                'name' => 'feed-post',
                'display_name' => '发送分享',
                'description' => '用户发送分享权限',
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'name' => 'feed-comment',
                'display_name' => '评论分享',
                'description' => '用户评论分享权限',
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'name' => 'feed-digg',
                'display_name' => '点赞分享',
                'description' => '用户点赞分享权限',
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'name' => 'feed-collection',
                'display_name' => '收藏分享',
                'description' => '用户收藏分享权限',
                'created_at' => $time,
                'updated_at' => $time,
            ],
        ]);

        $command->info('Install Successfully');
    }
}