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
            /*  id of discussion */
            $table->increments('id');

            /*  discussion proposition (title) */
            $table->longText('proposition');

            /*  discussion argument (body) */
            $table->longText('argument');

            /*  counts */
            $table->integer('reply_count')->default(0);
            $table->integer('vote_count')->default(0);
            $table->integer('pa_vote_count')->default(0);
            $table->integer('pv_vote_count')->default(0);

            /*  info */
            $table->integer('recent_action');
            $table->integer('post_date');
            $table->string('user_name');
            $table->integer('user_id');
            $table->string('current_phase')->default('pre-argument');

            /*  end dates */
            $table->integer('pa_end_date')->default(0);
            $table->integer('a_end_date')->default(0);
            $table->integer('v_end_date')->default(0);

            /*  pre argument votes */
            $table->integer('pa_for')->default(0);
            $table->integer('pa_against')->default(0);
            $table->integer('pa_undecided')->default(0);

            /*  post argument votes */
            $table->integer('pv_for')->default(0);
            $table->integer('pv_against')->default(0);

            /*  pre argument percent */
            $table->float('pa_for_per')->default(0);
            $table->float('pa_against_per')->default(0);
            $table->float('pa_undecided_per')->default(0);

            /*  post argument percent */
            $table->float('pv_for_per')->default(0);
            $table->float('pv_against_per')->default(0);

            /*  change percent diff */
            $table->float('for_change')->default(0);
            $table->float('against_change')->default(0);

            /* winner (for or against) */
            $table->string('winner')->default('not finished');
        });
    }

    public function down()
    {
        Schema::drop('discussions');
    }
}
