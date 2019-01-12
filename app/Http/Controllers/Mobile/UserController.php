<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use beevrr\Http\Controllers\Controller;
use beevrr\User;
use Illuminate\Support\Facades\Auth;
use beevrr\Http\Controllers\Auth\RegisterController;
use Validator;
use Hash;

class UserController extends Controller
{
    public function login()
    {
        $auth = ['user_name' => request('user_name'), 'password' =>
            request('password')];

        if(Auth::attempt($auth))
        {
            $user = Auth::user();
            $success['token'] =  $user->createToken('beevrr')->accessToken;

            return response()->json(['status' => 'success',
                'auth' => $success], 200);
        }
        else
        {
            return response()->json(['status' => 'failure'], 200);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => ['required', 'min:3', 'max:30', 'alpha_num',
                'string', 'unique:users'],
            'password' => ['required', 'min:3', 'string', 'same:passwordc'],
            'passwordc' => ['required', 'min:3', 'string', 'same:password'],
        ]);

        if($validator->fails())
        {
            return response()->json(['status'=>'failure'], 200);
        }

        User::create([
            'user_name' => $request->user_name,
            'password' => Hash::make($request->password),
            'score' => 0,
            'total_responses' => 0,
            'active_responses' => 0,
            'total_votes' => 0,
            'active_votes' => 0,
            'total_discussions' => 0,
            'active_discussions' => 0,
            'bio' => '',
        ]);

        return response()->json(['status' => 'success'], 200);
    }

    public function logout()
    {
        if(Auth::check())
        {
            Auth::logout();
            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['status' => 'failure'], 200);
    }
}
