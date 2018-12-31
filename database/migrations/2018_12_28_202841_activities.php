<?php
/*
 * beevrr
 * github.com/01mu
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Activities extends Migration
{
    public function up()
    {
        Schema::create('activities', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('action_type');
            $table->integer('action_link');
            $table->integer('date');
        });
    }

    public function down()
    {
        Schema::drop('activities');
    }
}
