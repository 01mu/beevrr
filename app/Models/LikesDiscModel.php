<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class LikesDiscModel extends Model
{
    protected $table = 'likes_disc';
    public $timestamps = false;
    protected $dateFormat = 'U';

    /* check if a user liked a discussion
     *
     * args:    $disc_id = discussion id
     * returns: whether the discussion has been liked
     */
    public static function check_liked($disc_id)
    {
        return LikesDiscModel::select('id')
            ->where('proposition', $disc_id)
            ->where('user_id', Auth::user()->id)
            ->get()
            ->first();
    }

    /* insert discussion like
     *
     * args:    $resp_id = discussion id
     * returns: none
     */
    public static function insert_like($disc_id)
    {
        $ins = new LikesDiscModel;

        $ins->user_id = Auth::user()->id;
        $ins->user_name = Auth::user()->user_name;
        $ins->proposition = $disc_id;
        $ins->date = time();
        $ins->save();
    }

    /* remove discussion like
     *
     * args:    $disc_id = discussion id
     * returns: removed like
     */
    public static function remove_like($disc_id)
    {
        return LikesDiscModel::where('proposition', $disc_id)
            ->where('user_id', Auth::user()->id)
            ->delete();
    }
}
