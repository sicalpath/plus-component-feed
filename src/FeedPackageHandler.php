<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed;

use Zhiyi\Plus\Models\Comment;
use Illuminate\Console\Command;
use Zhiyi\Plus\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Zhiyi\Plus\Support\PackageHandler;

class FeedPackageHandler extends PackageHandler
{
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
