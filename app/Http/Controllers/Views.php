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
    /* show index page with discussions
     *
     * args:    $page = pagination
     * returns: view with discussions
     */
    public function index($page = 0, Request $request)
    {
        $pagination = config('global.pagination');

        $discussions = DiscussionModel::get_index($page, $pagination);

        if(Common::pagination_redirect($discussions, $page))
        {
            if($request['mobile'] && $page != 0)
            {
                $content['status'] = 'end_pagination';

                return response()->json($content, 200);
            }
            else if(!$request['mobile'])
            {
                return redirect('/');
            }
        }

        $content = Common::get_stats();
        $content['discussions'] = $discussions;
        $content['disc_count'] = count($discussions);
        $content['logged_in'] = Auth::check();

        $l = route('page', array( 'p' => $page - 1,));
        $r = route('page', array( 'p' => $page + 1,));

        $content['pagination'] = Common::get_pagination_next($l, $r, $page);

        return Common::mobile_or_view($request, 'index', $content);
    }

    /* show user dashboard
     *
     * args:    none
     * returns: if not logged in: notice redirect
     *          else: dashboard view with info
     */
    public function dashboard(Request $request)
    {
        $content =  Common::get_stats();
        $content['user'] = User::where('id', Auth::user()->id)->get();

        return Common::mobile_or_view($request, 'dashboard', $content);
    }

    public function search_view()
    {
        return view('search_submit');
    }

    /* show search results
     *
     * args:    $page = pagination
     *          $requst = post form data
     * results: if error: notice redirect
     *          else: page with search results
     */
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

        $result = DiscussionModel::search_results($query, $page, $pagination);
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

    /* change user password
     *
     * args:    $request = post form data
     * returns: notice redirect
     */
    public function change_pw(Request $request)
    {
        $validator = Validator::make($request->all(), array(
            'oldpw' => ['required', 'min:3', 'string', 'same:conoldpw'],
            'conoldpw' => ['required', 'min:3', 'string', 'same:oldpw'],
            'newpw' => ['required', 'min:3', 'string', 'same:connewpw'],
            'connewpw' => ['required', 'min:3', 'string', 'same:newpw'],));

        if($validator->fails())
        {
            return Common::mobile_or_msg($request, false, 'Invalid input!');
        }

        if(Hash::check($request->oldpw, Auth::user()->password))
        {
            User::update_profile('pass', Hash::make($request->newpw));
            Auth::logout();

            return Common::mobile_or_msg($request, true, 'Password changed!');
        }

        return Common::mobile_or_msg($request, false, 'Invalid input!');
    }

    /* change user bio
     *
     * args:    $request = post form data
     * returns: notice redirect
     */
    public function change_bio(Request $request)
    {
        $validator = Validator::make($request->all(), array(
            'bio' => ['required', 'max:500'],));

        if($validator->fails())
        {
            return Common::mobile_or_msg($request, false, 'Invalid input!');
        }

        User::update_profile('bio', strip_tags($request->bio));

        return Common::mobile_or_msg($request, true, 'Bio updated!');
    }

    /* return notice page with session flash message
     *
     * args:    none
     * returns: notice view
     */
    public function notice()
    {
        return view('notice')->with('notice', session()->get('notice'));
    }

    /* return login page
     *
     * args:    none
     * returns: login view
     */
    public function login()
    {
        return view('login');
    }

    /* return register page
     *
     * args:    none
     * returns: register view
     */
    public function register()
    {
        return view('register');
    }

    /* redirect to home
     *
     * args:    none
     * returns: home redirect
     */
    public function home_redirect()
    {
        return redirect('/');
    }
}
