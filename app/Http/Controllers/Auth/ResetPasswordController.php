<?php
/*
 * beevrr
 * github.com/01mu
 */

namespace beevrr\Http\Controllers\Auth;

use beevrr\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
    }
}
