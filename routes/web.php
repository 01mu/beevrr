<?php
/*
 * beevrr
 * github.com/01mu
 */

use Illuminate\Http\Request;

Route::get('/', 'Views@index')->name('home');
Route::get('/p', 'Views@index');
Route::get('/p/{p}', 'Views@index')->name('page');
Route::get('notice', 'Views@notice');
Route::get('search_view', 'Views@search_view')->name('search-view');
Route::get('search_view/{p}', 'Views@search_post')->name('search-post');

Route::group(['middleware' => ['disc-exists']], function ()
{
    Route::get('discussion_view/{id}', 'Discussion@disc_view')
        ->name('disc-view');
});

Route::group(['middleware' => ['logged-in']], function ()
{
    Route::get('dashboard', 'Views@dashboard')->name('dashboard');
    Route::post('change_bio', 'Views@change_bio')->name('change-bio');
    Route::post('change_password', 'Views@change_pw')->name('change-pw');
    Route::get('disc_submit', 'Discussion@disc_sub_view')
        ->name('disc-sub-view');
    Route::post('disc_submit', 'Discussion@disc_sub_post')
        ->name('disc-sub-post');
});

$m = ['logged-in', 'disc-exists', 'can-respond'];
Route::group(['middleware' => $m], function ()
{
    Route::get('resp_submit/{id}', 'Response@resp_view')->name('resp-view');
    Route::post('resp_submit/{id}', 'Response@resp_post')->name('resp-post');
});

$m = ['logged-in', 'disc-exists', 'can-vote'];
Route::group(['middleware' => $m], function ()
{
    Route::get('vote_submit/{phase}/{id}', 'Vote@vote_view')
        ->name('vote-view');
    Route::post('vote_submit/{phase}/{id}', 'Vote@vote_post')
        ->name('vote-post');
});

Route::group(['middleware' => ['user-exists']], function ()
{
    Route::get('user_view/{id}', 'User@user_view')
        ->name('user-view');
    Route::get('user_info/{id}/{option}', 'User@user_info')
        ->name('user-info');
    Route::get('user_info/{id}/{option}/p', 'User@user_info');
    Route::get('user_info/{id}/{option}/p/{p}', 'User@user_info')
        ->name('page-ui');
});

Route::get('login', 'Views@login');
Route::get('register', 'Views@register');
Route::post('login', 'Auth\LoginController@login')->name('login');
Route::post('register', 'Auth\RegisterController@register')->name('register');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('discussion_view', 'Views@home_redirect');
Route::get('reply_submit', 'Views@home_redirect');
Route::get('vote_submit', 'Views@home_redirect');
Route::get('user_view', 'Views@home_redirect');
Route::get('user_info/{id}', 'Views@home_redirect');
Route::get('logout', 'Views@home_redirect');
Route::get('change_password', 'Views@home_redirect');
Route::get('change_bio', 'Views@home_redirect');

if(config('global.use_https'))
{
    URL::forceScheme('https');
}
