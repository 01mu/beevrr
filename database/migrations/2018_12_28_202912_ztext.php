<?php
/*
 * beevrr
 * github.com/01mu
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Ztext extends Migration
{
    public function up()
    {
        Schema::create('ztext', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('key_name');
            $table->string('key_value');
        });
    }

    public function down()
    {
        Schema::drop('ztext');
    }
}
