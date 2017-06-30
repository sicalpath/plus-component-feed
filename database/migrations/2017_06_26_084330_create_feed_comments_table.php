<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feed_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('评论者UID');
            $table->integer('to_user_id')->unsigned()->comment('被评论的作者');
            $table->integer('reply_to_user_id')->unsigned()->nullable()->default(0)->comment('被回复的评论作者');
            $table->integer('feed_id')->unsigned()->comment('动态ID');
            $table->text('comment_content')->comment('评论内容');
            $table->bigInteger('comment_mark')->comment('唯一标记');
            $table->tinyInteger('pinned')->unsigned()->nullable()->default(0)->comment('固定（置顶）动态，0-否，1-是');
            $table->integer('pinned_amount')->unsigned()->nullable()->default(0)->comment('固定金额，这些用于需求排序');
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('to_user_id');
            $table->index('reply_to_user_id');
            $table->index('feed_id');
            $table->unique('comment_mark');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feed_comments');
    }
}
