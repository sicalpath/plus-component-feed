<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

$component_table_name = 'feed_comments';

if (!Schema::hasColumn($component_table_name, 'user_id')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->integer('user_id')->index()->unsigned()->comment('评论者UID');
    });
}

if (!Schema::hasColumn($component_table_name, 'to_user_id')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->integer('to_user_id')->unsigned()->index()->comment('被评论的作者');
    });
}

if (!Schema::hasColumn($component_table_name, 'reply_to_user_id')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->integer('reply_to_user_id')->unsigned()->index()->comment('被回复的评论作者');
    });
}

if (!Schema::hasColumn($component_table_name, 'feed_id')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->integer('feed_id')->index()->unsigned()->comment('动态ID');
    });
}

if (!Schema::hasColumn($component_table_name, 'comment_content')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->text('comment_content')->comment('评论内容');
    });
}

if (!Schema::hasColumn($component_table_name, 'comment_mark')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->bigInteger('comment_mark')->default(0)->comment('移动端存储标记');
    });
}
