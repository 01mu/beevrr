<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use DB;
use Auth;
use DateTime;

class Common extends Controller
{
    public static function check_can_reply($disc_id)
    {
        $can_reply = 1;

        if(!Auth::check())
        {
            $can_reply = 0;
        }
        else
        {
            $user_id = Auth::user()->id;

            $poster_id = DB::select('SELECT user_id FROM discussions
                WHERE id = ?', [$disc_id])[0]->user_id;

            $has_resp = DB::select('SELECT * FROM responses
                WHERE proposition = ? AND user_id = ?', [$disc_id, $user_id]);

            $is_arg_phase = DB::select('SELECT current_phase FROM discussions
                WHERE id = ?', [$disc_id])[0]->current_phase === 'argument';

            if($user_id === $poster_id || $has_resp || !$is_arg_phase)
            {
                $can_reply = 0;
            }
        }

        return $can_reply;
    }

    public static function check_can_vote($disc_id, $phase)
    {
        $can_vote = 1;

        if(!Auth::check())
        {
            $can_vote = 0;
        }
        else
        {
            $user_id = Auth::user()->id;

            $same = DB::select('SELECT * FROM discussions
                WHERE id = ? AND user_id = ?', [$disc_id, $user_id]);

            $has_resp = DB::select('SELECT * FROM responses
                WHERE proposition = ? AND user_id = ?', [$disc_id, $user_id]);

            if($phase === 'post-argument')
            {
                $cant_vote = 1;

                $voted_pre = DB::select('SELECT * FROM votes
                    WHERE proposition = ? AND user_id = ? AND phase = ?',
                    [$disc_id, $user_id, 'pre-argument']);

                $voted_post = DB::select('SELECT * FROM votes
                    WHERE proposition = ? AND user_id = ? AND phase = ?',
                    [$disc_id, $user_id, 'post-argument']);

                if($voted_pre)
                {
                    $cant_vote = 0;
                }

                if($voted_post)
                {
                    $cant_vote = 1;
                }
            }
            else
            {
                $cant_vote = 0;

                $voted_pre = DB::select('SELECT * FROM votes
                    WHERE proposition = ? AND user_id = ? AND phase = ?',
                    [$disc_id, $user_id, 'pre-argument']);

                if($voted_pre)
                {
                    $cant_vote = 1;
                }
            }

            $prop = DB::select('SELECT * FROM discussions
                WHERE id = ?', [$disc_id])[0]->current_phase;

            $not_pre = $prop !== 'pre-argument';
            $not_post = $prop !== 'post-argument';

            if($same || $has_resp || $cant_vote || ($not_pre && $not_post))
            {
                $can_vote = 0;
            }
        }

        return $can_vote;
    }

    public static function check_exists($id)
    {
        $q = 'SELECT * FROM discussions WHERE id = ?';
        $a = [$id];

        if($select = DB::select($q, $a))
        {
            $exists = 1;
        }
        else
        {
            $exists = 0;
        }

        return $exists;
    }

    public static function notice_msg($msg)
    {
        session()->flash('notice', $msg);

        return redirect('notice');
    }

    public static function get_stats()
    {
        $content = array();

        $uc = DB::select('SELECT COUNT(*) AS count FROM users');
        $ud = DB::select('SELECT COUNT(*) AS count FROM discussions');
        $rc = DB::select('SELECT COUNT(*) AS count FROM responses');
        $vc = DB::select('SELECT COUNT(*) AS count FROM votes');

        $content['user_count'] = $uc;
        $content['discussion_count'] = $ud;
        $content['response_count'] = $rc;
        $content['vote_count'] = $vc;

        return $content;
    }

    public static function tm($datetime, $full = false)
    {
        $datetime = '@' . $datetime;
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );

        foreach ($string as $k => &$v)
        {
            if($diff->$k)
            {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            }
            else
            {
                unset($string[$k]);
            }
        }

        if(!$full)
        {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
}
