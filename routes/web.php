<?php
/*
 * beevrr
 * github.com/01mu
 */

use Illuminate\Http\Request;

// single
Route::get('/', 'Views@index')->name('home');
Route::get('/p', 'Views@index');
Route::get('/p/{p}', 'Views@index')->name('page');
Route::get('dashboard', 'Views@dashboard')->name('dashboard');
Route::get('notice', 'Views@notice');

// discussion, response, vote
Route::get('disc_submit', 'Discussion@disc_sub_view')->name('disc-sub-view');
Route::post('disc_submit', 'Discussion@disc_sub_post')->name('disc-sub-post');
Route::get('resp_submit/{id}', 'Response@resp_view')->name('resp-view');
Route::post('resp_submit/{id}', 'Response@resp_post')->name('resp-post');
Route::get('vote_submit/{phase}/{id}', 'Vote@vote_view')->name('vote-view');
Route::post('vote_submit/{phase}/{id}', 'Vote@vote_post')->name('vote-post');

// views item
Route::get('discussion_view/{id}', 'Discussion@disc_view')->name('disc-view');
Route::get('user_view/{id}', 'Views@user_view')->name('user-view');

// login, register, password
Route::get('login', 'Views@login');
Route::get('register', 'Views@register');
Route::post('change_password', 'Views@change_pw')->name('change-pw');

Route::post('login', 'Auth\LoginController@login')->name('login');
Route::post('register', 'Auth\RegisterController@register')->name('register');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// redirect
Route::get('discussion_view', 'Views@home_redirect');
Route::get('reply_submit', 'Views@home_redirect');
Route::get('vote_submit', 'Views@home_redirect');
Route::get('user_view', 'Views@home_redirect');
Route::get('logout', 'Views@home_redirect');
Route::get('change_password', 'Views@home_redirect');
