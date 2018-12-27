<?php
/*
 * beevrr
 * github.com/01mu
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Discussions extends Migration
{
    public function up()
    {
        Schema::create('discussions', function (Blueprint $table)
        {
            $table->increments('id');
            $table->longText('proposition');
            $table->longText('argument');
            $table->integer('post_count');
            $table->integer('post_date');
            $table->string('pa_phase');
            $table->string('a_phase');
            $table->string('v_phase');
            $table->string('user_name');
            $table->integer('user_id');
            $table->string('current_phase');
            $table->integer('a_phase_date');
            $table->integer('v_phase_date');
            $table->integer('end_date');
            $table->integer('pa_for');
            $table->integer('pa_against');
            $table->integer('pa_undecided');
            $table->integer('pv_for');
            $table->integer('pv_against');
            $table->integer('vote_count_pa');
            $table->integer('vote_count_pv');
            $table->float('pa_for_per');
            $table->float('pa_against_per');
            $table->float('pa_undecided_per');
            $table->float('pvfor_per');
            $table->float('pv_against_per');
            $table->float('for_change');
            $table->float('against_change');
            $table->string('winner');
        });
    }

    public function down()
    {
        Schema::drop('discussions');
    }
}
