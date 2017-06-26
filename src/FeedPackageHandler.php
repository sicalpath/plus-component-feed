<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed;

use Carbon\Carbon;
use Zhiyi\Plus\Models\Comment;
use Illuminate\Console\Command;
use Zhiyi\Plus\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Zhiyi\Plus\Support\PackageHandler;
use Illuminate\Database\Schema\Blueprint;

class FeedPackageHandler extends PackageHandler
{
    public function removeHandle($command)
    {
        if ($command->confirm('This will delete your datas for feeds, continue?')) {
            Comment::where('component', 'feed')->delete();
            Permission::whereIn('name', ['feed-post', 'feed-comment', 'feed-digg', 'feed-collection'])->delete();
            Schema::dropIfExists('feed_atmes');
            Schema::dropIfExists('feed_diggs');
            Schema::dropIfExists('feed_comments');
            Schema::dropIfExists('feed_storages');
            Schema::dropIfExists('feed_collections');

            $command->info('The Feed Component has been removed');
        }
    }

    /**
     * Install handler.
     *
     * @param \Illuminate\Console\Command $command
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function installHandle($command)
    {
        // publish public assets
        $command->call('vendor:publish', [
            '--provider' => FeedServiceProvider::class,
            '--tag' => 'public',
            '--force' => true,
        ]);

        // Run the database migrations
        $command->call('migrate');

        // Run the database seeds.
        $command->call('db:seed', [
            '--class' => \FeedDatabaseAllSeeder::class,
        ]);
    }

    /**
     * Create a migration file.
     *
     * @param \Illuminate\Console\Command $command
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function createMigrationHandle(Command $command)
    {
        $path = str_replace(app('path.base'), '', dirname(__DIR__).'/database/migrations');
        $table = $command->ask('Enter the table name');
        $name = sprintf('create_%s_table', $table);
        $create = $command->confirm('Is it creating a new migration');

        return $command->call('make:migration', [
            'name' => $name,
            '--path' => $path,
            '--table' => $table,
            '--create' => $create,
        ]);
    }
}
