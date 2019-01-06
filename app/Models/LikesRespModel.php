<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class LikesRespModel extends Model
{
    protected $table = 'likes_resp';
    public $timestamps = false;
    protected $dateFormat = 'U';

    /* check if a user liked a response
     *
     * args:    $resp_id = response id
     * returns: whether the response has been liked
     */
    public static function check_liked($resp_id)
    {
        return LikesRespModel::select('id')
            ->where('response', $resp_id)
            ->where('user_id', Auth::user()->id)
            ->get()
            ->first();
    }

    /* insert response like
     *
     * args:    $resp_id = response id
     * returns: none
     */
    public static function insert_like($resp_id)
    {
        $ins = new LikesRespModel;

        $ins->user_id = Auth::user()->id;
        $ins->user_name = Auth::user()->user_name;
        $ins->response = $resp_id;
        $ins->date = time();
        $ins->save();
    }

    /* remove response like
     *
     * args:    $resp_id = response id
     * returns: removed like
     */
    public static function remove_like($resp_id)
    {
        return LikesRespModel::where('response', $resp_id)
            ->where('user_id', Auth::user()->id)
            ->delete();
    }
}
