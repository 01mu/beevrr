<?php
/*
 * beevrr
 * github.com/01mu
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LikesResp extends Migration
{
    public function up()
    {
        Schema::create('likes_resp', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('user_name');
            $table->integer('response');
            $table->integer('date');
        });
    }

    public function down()
    {
        Schema::drop('likes_resp');
    }
}
