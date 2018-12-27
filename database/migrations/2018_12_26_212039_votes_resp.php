<?php
/*
 * beevrr
 * github.com/01mu
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VotesResp extends Migration
{
    public function up()
    {
        Schema::create('votes_resp', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('response');
            $table->integer('user_id');
            $table->string('user_name');
            $table->integer('vote');
            $table->integer('type');
            $table->integer('date');
        });
    }

    public function down()
    {
        Schema::drop('votes_resp');
    }
}
