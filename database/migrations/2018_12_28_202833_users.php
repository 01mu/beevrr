<?php
/*
 * beevrr
 * github.com/01mu
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Users extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('user_name')->unique();
            $table->text('password');
            $table->integer('score');
            $table->integer('total_responses');
            $table->integer('active_responses');
            $table->integer('total_votes');
            $table->integer('active_votes');
            $table->integer('total_discussions');
            $table->integer('active_discussions');
            $table->longText('bio');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('users');
    }
}
