<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

$component_table_name = 'feed_diggs';

if (!Schema::hasColumn($component_table_name, 'user_id')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->integer('user_id')->index()->unsigned()->comment('者UID');
    });
}

if (!Schema::hasColumn($component_table_name, 'feed_id')) {
    Schema::table($component_table_name, function (Blueprint $table) {
        $table->integer('feed_id')->index()->unsigned()->comment('动态ID');
    });
}

