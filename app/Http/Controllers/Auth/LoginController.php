<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Controllers\Auth;

use beevrr\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        if(Auth::attempt(['user_name' => $request->user_name,
            'password' => $request->password]))
        {
            return $this->sendLoginResponse($request);
        }
        else
        {
            return $this->sendFailedLoginResponse($request, 'auth.failed_status');
        }
    }

    /*public function showLoginForm()
    {
        return view('login');
    }*/
}
