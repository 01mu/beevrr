<?php
/*
 * beevrr
 * github.com/01mu
 */

use Illuminate\Http\Request;

Route::get('/', function ()
{
    $content = array();

    $users = DB::select('SELECT * FROM users');
    $uc = DB::select('SELECT COUNT(*) AS count FROM users');

    $content[] = $users;
    $content[] = $uc;

    return view('welcome')->with('content', $content);
});

Route::get('login', function ()
{
    return view('login');
});

//Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('login');

/* logout */
Route::get('logout', function()
{
    abort(404);
});

Route::post('logout', 'Auth\LoginController@logout')->name('logout');

/* register */
//Route::get('register', 'Auth\RegisterController@showRegistrationForm')
//    ->name('register');

Route::get('register', function ()
{
    return view('register');
});

Route::post('register', 'Auth\RegisterController@register')->name('register');;

