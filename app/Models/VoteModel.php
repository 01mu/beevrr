<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class VoteModel extends Model
{
    protected $table = 'votes';
    public $timestamps = false;
    protected $dateFormat = 'U';

    public static function insert($opinion, $disc_id, $phase, $time)
    {
        $vote_insert = new VoteModel;

        $vote_insert->opinion = $opinion;
        $vote_insert->proposition = $disc_id;
        $vote_insert->user_id = Auth::user()->id;
        $vote_insert->user_name = Auth::user()->user_name;
        $vote_insert->phase = $phase;
        $vote_insert->date = $time;
        $vote_insert->save();
    }

    public static function get_user_vote($disc_id, $user_id, $type)
    {
        return VoteModel::select('opinion')
            ->where('proposition', $disc_id)
            ->where('phase', $type)
            ->where('user_id', $user_id)
            ->get()
            ->first();
    }
}
