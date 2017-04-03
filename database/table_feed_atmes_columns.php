<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

$component_table_name = 'feed_atmes';

if (! Schema::hasColumn($component_table_name, 'at_user_id')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->integer('at_user_id')->unsigned()->index()->comment('被@用户ID');
    });
}

if (! Schema::hasColumn($component_table_name, 'user_id')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->integer('user_id')->index()->unsigned()->comment('主动@用户ID');
    });
}

if (! Schema::hasColumn($component_table_name, 'feed_id')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->integer('feed_id')->index()->unsigned()->comment('动态id');
    });
}
