<?php
/*
 * beevrr
 * github.com/01mu
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Responses extends Migration
{
    public function up()
    {
        Schema::create('responses', function (Blueprint $table)
        {
            $table->increments('id');
            $table->longText('response');
            $table->integer('proposition');
            $table->integer('user_id');
            $table->string('user_name');
            $table->integer('opinion');
            $table->integer('date');
            $table->integer('score');
        });
    }

    public function down()
    {
        Schema::drop('responses');
    }
}
