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
            // id of discussion
            $table->increments('id');

            // discussion proposition (title)
            $table->longText('proposition');

            // discussion argument (body)
            $table->longText('argument');

            // counts
            $table->integer('reply_count');
            $table->integer('vote_count');
            $table->integer('pa_vote_count');
            $table->integer('pv_vote_count');

            // info
            $table->integer('recent_action');
            $table->integer('post_date');
            $table->string('user_name');
            $table->integer('user_id');
            $table->string('current_phase');

            // end dates
            $table->integer('pa_end_date');
            $table->integer('a_end_date');
            $table->integer('v_end_date');

            // pre argument votes
            $table->integer('pa_for');
            $table->integer('pa_against');
            $table->integer('pa_undecided');

            // post argument votes
            $table->integer('pv_for');
            $table->integer('pv_against');

            // pre argument percent
            $table->float('pa_for_per');
            $table->float('pa_against_per');
            $table->float('pa_undecided_per');

            // post argument percent
            $table->float('pv_for_per');
            $table->float('pv_against_per');

            // change percent diff
            $table->float('for_change');
            $table->float('against_change');

            // winner (for or against)
            $table->string('winner');
        });
    }

    public function down()
    {
        Schema::drop('discussions');
    }
}
