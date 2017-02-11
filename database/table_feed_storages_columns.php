<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

$component_table_name = 'feed_storages';

if (!Schema::hasColumn($component_table_name, 'user_id')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->integer('user_id')->index()->comment('作者UID');
    });
}

if (!Schema::hasColumn($component_table_name, 'feed_id')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->integer('feed_id')->index()->unsigned()->comment('动态ID');
    });
}

if (!Schema::hasColumn($component_table_name, 'feed_storage_id')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->integer('feed_storage_id')->unsigned()->comment('附件ID');
    });
}