<?php
/*
 * beevrr
 * github.com/01mu
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VotesDisc extends Migration
{
    public function up()
    {
        Schema::create('votes_disc', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('proposition');
            $table->integer('user_id');
            $table->string('user_name');
            $table->integer('vote');
            $table->integer('type');
            $table->integer('date');
            $table->integer('hidden');
        });
    }

    public function down()
    {
        Schema::drop('votes_disc');
    }
}
