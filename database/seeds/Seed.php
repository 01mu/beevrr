<?php
/*
 * beevrr
 * github.com/01mu
 */

use Illuminate\Database\Seeder;

class Seed extends Seeder
{
    public function run()
    {
        $ins = ['response' => 'asd',
        'proposition' => 1,
        'user_id' => 3,
        'user_name' => 'asd',
        'opinion' => 222,
        'date' => 33,
        'score' => 2];

        for($i = 0; $i < 44; $i++)
        DB::table('users')->insert($ins);
    }
}
