<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class ResponseModel extends Model
{
    protected $table = 'responses';
    public $timestamps = false;
    protected $dateFormat = 'U';

    /* insert new response
     *
     * args:    $response = response text
     *          $disc_id = discussion id
     *          $opinion = opinion (for or against)
     *          $time = current time
     * returns: none
     */
    public static function insert($response, $disc_id, $opinion, $time)
    {
        $discussion_insert = new ResponseModel;

        $discussion_insert->response = $response;
        $discussion_insert->proposition = $disc_id;
        $discussion_insert->user_id = Auth::user()->id;
        $discussion_insert->user_name = Auth::user()->user_name;
        $discussion_insert->opinion = $opinion;
        $discussion_insert->date = $time;
        $discussion_insert->save();
    }
}
