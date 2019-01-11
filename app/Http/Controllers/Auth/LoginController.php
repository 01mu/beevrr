<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Controllers\Auth;

use beevrr\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
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
        $captcha = Validator::make(Input::all(), array(
            'captcha' => 'required|captcha',));

        if($captcha->fails())
        {
            session()->flash('notice', 'Bad CAPTCHA!');

            return redirect('notice');
        }

        $auth = ['user_name' => $request->user_name,
            'password' => $request->password];

        if(Auth::attempt($auth))
        {
            return $this->sendLoginResponse($request);
        }
        else
        {
            return $this->sendFailedLoginResponse($request,
                'auth.failed_status');
        }
    }
}
