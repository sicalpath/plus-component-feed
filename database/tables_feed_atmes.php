<?php

<<<<<<< HEAD
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

if(!$chema::hastable('feed_atmes')) {
   Schema::create('feed_atmes', function (Blueprint $table) {
        $table->engine = 'InnoDB';
        $table->increments('id');
        $table->integer('at_user_id')->comment('被@用户ID');
        $table->integer('user_id')->comment('主动@用户ID');
        $table->string('feed_id')->comment('动态id');
        $table->timestamps();

        $table->index('at_user_id']);
        $table->index('user_id']);
        $table->index('feed_id']);
    });
}
=======
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FeedAtmes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feed_atmes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('at_user_id')->comment('被@用户ID');
            $table->integer('user_id')->comment('主动@用户ID');
            $table->string('feed_id')->comment('动态id');
            $table->timestamps();

            $table->index('at_user_id']);
            $table->index('user_id']);
            $table->index('feed_id']);
        });
    }
>>>>>>> ba5bfe8f8e53e0009c9f7308af6ec2f4fb642bfd

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feed_atmes');
    }
}
