<?php
/*
 * beevrr
 * github.com/01mu
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Votes extends Migration
{
    public function up()
    {
        Schema::create('votes', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('proposition');
            $table->integer('user_id');
            $table->string('user_name');
            $table->string('opinion');
            $table->integer('date');
            $table->string('phase');
        });
    }

    public function down()
    {
        Schema::drop('votes');
    }
}
