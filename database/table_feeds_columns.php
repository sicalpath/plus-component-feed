<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

$component_table_name = 'feeds';

if (!Schema::hasColumn($component_table_name, 'user_id')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->integer('user_id')->index()->comment('作者UID');
    });
}

if (!Schema::hasColumn($component_table_name, 'feed_title')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->string('feed_title', 32)->nullable()->default('')->index()->comment('动态标题');
    });
}

if (!Schema::hasColumn($component_table_name, 'feed_content')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->text('feed_content')->comment('动态内容');
    });
}

if (!Schema::hasColumn($component_table_name, 'feed_from')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->tinyInteger('feed_from')->default(1)->unsigned()->comment('动态来源 1:pc 2:h5 3:ios 4:android 5:其他');
    });
}

if (!Schema::hasColumn($component_table_name, 'feed_digg_count')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->integer('feed_digg_count')->default(0)->unsigned()->comment('动态点赞数');
    });
}

if (!Schema::hasColumn($component_table_name, 'feed_view_count')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->integer('feed_view_count')->default(0)->unsigned()->comment('动态阅读数');
    });
}

if (!Schema::hasColumn($component_table_name, 'feed_comment_count')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->integer('feed_comment_count')->default(0)->unsigned()->comment('动态评论数');
    });
}

if (!Schema::hasColumn($component_table_name, 'feed_latitude')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->string('feed_latitude')->nullable()->default('')->comment('纬度');
    });
}

if (!Schema::hasColumn($component_table_name, 'feed_longtitude')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->string('feed_longtitude')->nullable()->default('')->comment('经度');
    });
}

if (!Schema::hasColumn($component_table_name, 'feed_geohash')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->string('feed_geohash')->nullable()->default('')->comment('GEO');
    });
}

if (!Schema::hasColumn($component_table_name, 'feed_client_id')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->string('feed_client_id')->nullable()->default('')->comment('发布IP');
    });
}

if (!Schema::hasColumn($component_table_name, 'feed_mark')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->bigInteger('feed_mark')->default(0)->comment('移动端存储标记');
    });
}