<?php
/*
 * beevrr
 * github.com/01mu
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLog extends Migration
{
    public function up()
    {
        Schema::create('update_log', function (Blueprint $table)
        {
            $table->increments('id');
            $table->longText('action');
            $table->longText('date');
        });
    }

    public function down()
    {
        Schema::drop('update_log');
    }
}
