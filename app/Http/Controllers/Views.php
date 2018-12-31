<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;

use beevrr\Http\Controllers\Common;

use Validator;
use DB;
use Auth;
use DateTime;

class Views extends Controller
{
    public function index($p = 0)
    {
        if(!$p)
        {
            $offset = 0;
        }
        else
        {
            $offset = 10 * $p;
        }

        $disc = DB::select('SELECT * FROM discussions
            ORDER BY recent_action DESC LIMIT 10 OFFSET ?', [$offset]);

        for($i = 0; $i < count($disc); $i++)
        {
            $disc[$i]->post_date = Common::tm($disc[$i]->post_date);
        }

        if(!count($disc))
        {
            return redirect('/');
        }

        $content = Common::get_stats();
        $content['discussions'] = $disc;
        $content['page'] = $p + 1;
        $content['left'] = $p - 1;

        return view('index')->with('content', $content);
    }

    public function user_view($id)
    {
        $q = 'SELECT * FROM users WHERE id = ?';
        $a = [$id];

        if($select = DB::select($q, $a))
        {
            $content = Common::get_stats();
            $content['user'] = $select;

            $view = view('user_view')->with('content', $content);
        }
        else
        {
            $view = Common::notice_msg('Invalid ID!');
        }

        return $view;
    }

    public function dashboard()
    {
        if($user = Auth::user())
        {
            $q = 'SELECT * FROM users WHERE id = ?';
            $a = [Auth::user()->id];

            $content =  Common::get_stats();
            $content['user'] = DB::select($q, $a);

            $view = view('dashboard')->with('content', $content);
        }
        else
        {
            $view = redirect('login');
        }

        return $view;
    }

    public function notice()
    {
        return view('notice')->with('notice', session()->get('notice'));
    }

    public function login()
    {
        return view('login');
    }

    public function register()
    {
        return view('register');
    }

    public function change_pw(Request $request)
    {
        if(!Auth::check())
        {
            return Common::notice_msg('Not logged in!');
        }

        $validator = Validator::make($request->all(), array(
            'oldpw' => ['required', 'min:3', 'string', 'same:conoldpw'],
            'conoldpw' => ['required', 'min:3', 'string', 'same:oldpw'],
            'newpw' => ['required', 'min:3', 'string', 'same:connewpw'],
            'connewpw' => ['required', 'min:3', 'string', 'same:newpw'],));

        if($validator->fails())
        {
            return Common::notice_msg('Invalid input!');
        }
        else
        {
            $curpw = Auth::user()->password;
            $oldpw = $request->oldpw;

            if(Hash::check($oldpw, $curpw))
            {
                $update  = DB::update(
                    'UPDATE users SET password = ? WHERE id = ?',
                    [Hash::make($request->newpw), Auth::user()->id]);

                Auth::logout();

                return Common::notice_msg('Password changed!');
            }
            else
            {
                return Common::notice_msg('Invalid input!');
            }
        }
    }

    public function home_redirect()
    {
        return redirect('/');
    }
}
