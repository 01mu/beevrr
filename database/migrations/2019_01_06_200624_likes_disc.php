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
        Schema::create('likes_disc', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('user_name');
            $table->integer('proposition');
            $table->integer('date');
        });
    }

    public function down()
    {
        Schema::drop('likes_disc');
    }
}
