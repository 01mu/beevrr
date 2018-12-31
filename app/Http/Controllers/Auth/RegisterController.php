<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Controllers\Auth;

use beevrr\User;
use beevrr\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        $rules = ['captcha' => 'required|captcha'];
        $validator = Validator::make($data, $rules);

        if(!$validator->fails())
        {
            $validator = Validator::make($data, [
                'user_name' => ['required', 'min:3', 'max:30', 'alpha_num',
                    'string', 'unique:users'],

                'password' => ['required', 'min:3', 'string', 'confirmed'],
            ]);
        }

        return $validator;
    }

    protected function create(array $data)
    {
        return User::create([
            'user_name' => $data['user_name'],
            'password' => Hash::make($data['password']),
            'score' => 0,
            'total_responses' => 0,
            'active_responses' => 0,
            'total_votes' => 0,
            'active_votes' => 0,
            'total_discussions' => 0,
            'active_discussions' => 0,
        ]);
    }
}
