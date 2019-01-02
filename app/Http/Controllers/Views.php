<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Controllers;

use Illuminate\Http\Request;

use beevrr\Http\Controllers\Common;
use beevrr\Models\DiscussionModel;
use beevrr\User;

use Auth;
use Validator;
use Hash;

class Views extends Controller
{
    public function index($page = 0)
    {
        $pagination = config('global.pagination');

        $discussions = DiscussionModel::orderBy('recent_action', 'DESC')
            ->skip(Common::get_offset($page))
            ->take($pagination)
            ->get();

        Common::fix_time($discussions);

        if(Common::pagination_redirect($discussions, $page))
        {
            return redirect('/');
        }

        $content = Common::get_stats();
        $content['discussions'] = $discussions;

        $l = route('page', array( 'p' => $page - 1,));
        $r = route('page', array( 'p' => $page + 1,));

        $content['pagination'] = Common::get_pagination_next($l, $r, $page);

        return view('index')->with('content', $content);
    }

    public function dashboard()
    {
        if(!$user = Auth::user())
        {
            return Common::notice_msg('Not logged in!');
        }

        $content =  Common::get_stats();
        $content['user'] = User::where('id', Auth::user()->id)->get();

        return view('dashboard')->with('content', $content);
    }

    public function search_view()
    {
        return view('search_submit');
    }

    public function search_post($page = 0, Request $request)
    {
        $validator = Validator::make($request->all(), array(
            'q' => ['required', 'string'],));

        if($validator->fails())
        {
            return Common::notice_msg('Invalid input!');
        }

        $pagination = config('global.pagination');

        $query = '%' . $request->q . '%';

        $result = DiscussionModel::orderBy('recent_action', 'DESC')
            ->skip(Common::get_offset($page))
            ->take($pagination)
            ->where('proposition', 'like', $query)
            ->orWhere('proposition', 'like', $query)
            ->get();

        Common::fix_time($result);

        $content = Common::get_stats();
        $content['search'] = $result;

        $l = route('search-post', array(
                'q' => $request->q,
                'p' => $page - 1,));

        $r = route('search-post', array(
                'q' => $request->q,
                'p' => $page + 1,));

        $content['pagination'] = Common::get_pagination_next($l, $r, $page);

        return view('search_view')->with('content', $content);
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

        if(Hash::check($request->oldpw, Auth::user()->password))
        {
            $user_update = User::find(Auth::user()->id);

            $user_update->password = Hash::make($request->newpw);
            $user_update->save();

            Auth::logout();

            return Common::notice_msg('Password changed!');
        }

        return Common::notice_msg('Invalid input!');
    }

    public function change_bio(Request $request)
    {
        if(!Auth::check())
        {
            return Common::notice_msg('Not logged in!');
        }

        $validator = Validator::make($request->all(), array(
            'bio' => ['required', 'max:500'],));

        if($validator->fails())
        {
            return Common::notice_msg('Invalid input!');
        }

        $user_update = User::find(Auth::user()->id);

        $user_update->bio = $request->bio;
        $user_update->save();

        return Common::notice_msg('Bio updated!');
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

    public function home_redirect()
    {
        return redirect('/');
    }
}
