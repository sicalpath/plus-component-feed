<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePinnedCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pinned_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('comment_id')->unsigned()->comment('评论ID');
            $table->integer('day')->comment('固定期限，单位 天');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pinned_comments');
    }
}
