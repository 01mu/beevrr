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

            return response()->json(['status'=>'success',
                'auth' => $success], 200);
        }
        else
        {
            return response()->json(['status'=>'failure'], 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), array(
            'user_name' => ['required', 'min:3', 'max:30', 'alpha_num',
            'string', 'unique:users'],
            'password' => ['required', 'min:3', 'string', 'confirmed'],));

        if($validator->fails())
        {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = User::new_user($request->all());

        $success['token'] =  $user->createToken('beevrr')->accessToken;
        $success['user_name'] =  $user->user_name;

        return response()->json(['status'=>'success',
            'auth' => $success], 200);
    }

    public function logout()
    {
        if(Auth::check())
        {
            Auth::logout();
            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['status'=>'failure'], 401);
    }

    public function details()
    {
        if(Auth::check())
        {
            return response()->json(['status'=>'success',
                'auth' => Auth::user()], 200);
        }

        return response()->json(['status'=>'failure'], 401);
    }
}
