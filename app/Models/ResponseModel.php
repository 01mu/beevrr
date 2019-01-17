<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Models;

use Illuminate\Database\Eloquent\Model;

use beevrr\Http\Controllers\Response;
use beevrr\Http\Controllers\Common;

use Auth;

class ResponseModel extends Model
{
    protected $table = 'responses';
    public $timestamps = false;
    protected $dateFormat = 'U';

    public static function disc_responses_pag($type, $disc_id, $page, $pagination)
    {
        $responses = ResponseModel::select('*')
            ->where('proposition', $disc_id)
            ->where('opinion', $type)
            ->orderBy('score', 'DESC')
            ->skip(Common::get_offset($page))
            ->take($pagination)
            ->get();

        Common::fix_time($responses, 1);

        for($i = 0; $i < count($responses); $i++)
        {
            $res = $responses[$i];

            $res['liked'] = Response::get_like_text($res->id);
        }

        return $responses;
    }

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

    /* get for or against responses for a discussion and fix times
     *
     * args:    $type = for or against
     *          $disc_id = discussion id
     * returns: none
     */
    public static function disc_responses($type, $disc_id)
    {
        $responses = ResponseModel::select('*')
            ->where('proposition', $disc_id)
            ->where('opinion', $type)
            ->orderBy('score', 'DESC')
            ->get();

        Common::fix_time($responses, 1);

        for($i = 0; $i < count($responses); $i++)
        {
            $res = $responses[$i];

            $res['liked'] = Response::get_like_text($res->id);
        }

        return $responses;
    }

    /* select user's response to a discussion
     *
     * args:    $disc_id = discussion id
     *          $user_id = user id
     * returns: none
     */
    public static function user_response($disc_id, $user_id)
    {
        return ResponseModel::select('opinion')
            ->where('proposition', $disc_id)
            ->where('user_id', $user_id)
            ->get()
            ->first();
    }

    /* increment or decrement like count for response
     *
     * args:    $disc_id = discussion id
     *          $type = decrement or incrememnt
     * returns: none
     */
    public static function make_like($resp_id, $type)
    {
        $up = ResponseModel::find($resp_id);

        switch($type)
        {
            case 0:
                $up->score -= 1;
                break;
            default:
                $up->score += 1;
                break;
        }

        $up->save();
    }

    /* select based on response id
     *
     * args:    $resp_id = response id
     * return:  selected response
     */
    public static function select_from($resp_id)
    {
        $resp = ResponseModel::select('*')
            ->where('id', $resp_id)
            ->get()
            ->first();

        return $resp;
    }
}
