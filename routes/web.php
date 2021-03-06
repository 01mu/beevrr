<?php
/*
 * beevrr
 * github.com/01mu
 */

use Illuminate\Http\Request;

Route::group([], function ()
{
    Route::get('/', 'Views@index')
        ->name('home');
    Route::get('/p', 'Views@index')
        ->name('page-h');
    Route::get('/p/{p}', 'Views@index')
        ->name('page');
    Route::get('notice', 'Views@notice')
        ->name('notice');
    Route::get('search_view', 'Views@search_view')
        ->name('search-view');
    Route::get('search_view/{p}', 'Views@search_post')
        ->name('search-post');
});
Route::group(['middleware' => ['disc']], function ()
{
    Route::get('discussion_view/{id}', 'Discussion@disc_view')
        ->name('disc-view');
});

Route::group(['middleware' => ['log']], function ()
{
    Route::get('dashboard', 'Views@dashboard')
        ->name('dashboard');
    Route::post('change_bio', 'Views@change_bio')
        ->name('change-bio');
    Route::post('change_password', 'Views@change_pw')
        ->name('change-pw');
    Route::get('disc_submit', 'Discussion@disc_sub_view')
        ->name('disc-sub-view');
    Route::post('disc_submit', 'Discussion@disc_sub_post')
        ->name('disc-sub-post');
});

Route::group(['middleware' => ['log', 'disc', 'throttle:3,1']], function ()
{
    Route::get('disc_like/{id}', 'Discussion@disc_like')
        ->name('disc-like');
});

Route::group(['middleware' => ['log', 'resp-e', 'throttle:5,1']], function ()
{
    Route::get('resp_like/{id}', 'Response@resp_like')
        ->name('resp-like');
});

Route::group(['middleware' => ['log', 'disc', 'resp']], function ()
{
    Route::get('resp_submit/{id}', 'Response@resp_view')
        ->name('resp-view');
    Route::post('resp_submit/{id}', 'Response@resp_post')
        ->name('resp-post');
    Route::post('resp_like/{id}', 'Response@resp_like')
        ->name('resp-like');
});

Route::group(['middleware' => ['log', 'disc', 'vote']], function ()
{
    Route::get('vote_submit/{phase}/{id}', 'Vote@vote_view')
        ->name('vote-view');
    Route::post('vote_submit/{phase}/{id}', 'Vote@vote_post')
        ->name('vote-post');
});

Route::group(['middleware' => ['user']], function ()
{
    Route::get('user_view/{id}', 'User@user_view')
        ->name('user-view');
    Route::get('user_info/{id}/{option}', 'User@user_info')
        ->name('user-info');
    Route::get('user_info/{id}/{option}/p', 'User@user_info')
        ->name('page-ui-h');
    Route::get('user_info/{id}/{option}/p/{p}', 'User@user_info')
        ->name('page-ui');
});

Route::group([], function ()
{
    Route::get('login', 'Views@login')
        ->name('view-login');
    Route::get('register', 'Views@register')
        ->name('view-reg');
    Route::post('login', 'Auth\LoginController@login')
        ->name('login');
    Route::post('register', 'Auth\RegisterController@register')
        ->name('register');
    Route::post('logout', 'Auth\LoginController@logout')
        ->name('logout');
});

Route::group([], function ()
{
    Route::get('discussion_view', 'Views@home_redirect');
    Route::get('reply_submit', 'Views@home_redirect');
    Route::get('vote_submit', 'Views@home_redirect');
    Route::get('user_view', 'Views@home_redirect');
    Route::get('user_info/{id}', 'Views@home_redirect');
    Route::get('logout', 'Views@home_redirect');
    Route::get('change_password', 'Views@home_redirect');
    Route::get('change_bio', 'Views@home_redirect');
});

Route::group(['middleware' => ['setm', 'log', 'throttle:3,10']], function ()
{
    Route::post('mobile/disc_submit', 'Discussion@disc_sub_post');
});

Route::group(['middleware' => ['setm', 'log']], function ()
{
    Route::get('mobile/dashboard', 'Views@dashboard');
    Route::post('mobile/change_bio', 'Views@change_bio');
    Route::post('mobile/change_password', 'Views@change_pw');
});

Route::group(['middleware' => ['setm', 'log', 'disc', 'resp',
                               'throttle:3,10']], function ()
{
    Route::post('mobile/resp_submit/{id}', 'Response@resp_post');
});

Route::group(['middleware' => ['setm', 'log', 'disc', 'vote',
                               'throttle:3,10']], function ()
{
    Route::post('mobile/vote_submit/{phase}/{id}', 'Vote@vote_post');
});

Route::group(['middleware' => ['setm', 'log', 'disc',
                               'throttle:3,10']], function ()
{
    Route::get('mobile/disc_like/{id}', 'Discussion@disc_like');
});

Route::group(['middleware' => ['setm', 'log', 'resp-e',
                               'throttle:5,10']], function ()
{
   Route::get('mobile/resp_like/{id}', 'Response@resp_like');
});

Route::group(['middleware' => ['setm', 'disc']], function ()
{
    Route::get('mobile/discussion_view/{id}', 'Discussion@disc_view');
    Route::get('mobile/get_resp/{type}/{id}/p/{p}', 'Discussion@get_responses');
});

Route::group(['middleware' => ['setm']], function ()
{
    Route::get('mobile/user_info/{id}/{option}/p/{p}', 'User@user_info');
    Route::get('mobile/home/p/{p}', 'Views@index');
});

Route::group(['middleware' => ['throttle:3,20']], function ()
{
    Route::post('mobile/login', 'User@login');
    Route::post('mobile/register', 'User@register');
});

Route::group([], function ()
{
    Route::post('mobile/logout', 'User@logout');
});

if(config('global.use_https'))
{
    URL::forceScheme('https');
}
