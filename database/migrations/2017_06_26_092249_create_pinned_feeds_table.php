<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePinnedFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pinned_feeds', function (Blueprint $table) {
            $table->increments('id')->comment('固定申请');
            $table->integer('feed_id')->unsigned()->comment('动态ID');
            $table->integer('day')->comment('固定期限，单位 天');
            $table->timestamp('expire_at')->nullabel()->comment('到期时间，固定后设置该时间');
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
        Schema::dropIfExists('pinned_feeds');
    }
}
