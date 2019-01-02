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
            $table->string('user_name');

            /*
             * 0 = pre-vote for
             * 1 = pre-vote against
             * 2 = pre-vote undecided
             * 3 = post-vote for
             * 4 = post-vote against
             * 5 = response for
             * 6 = response against
             * 7 = new discussion
             */
            $table->integer('action_type');
            $table->integer('proposition');
            $table->integer('is_active')->default(1);
            $table->integer('date');
        });
    }

    public function down()
    {
        Schema::drop('activities');
    }
}
