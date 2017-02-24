<?php

Auth::routes();
Route::get('/logout', 'Auth\LoginController@getLogout');
Route::get('/login_email', 'Auth\LoginController@showLoginEmailForm');
Route::post('/badge/verify', 'BadgeController@verify');

Route::get('/oauth_client/redirect/{provider}', 'OAuthClientController@redirect');
Route::get('/oauth_client/callback/{provider}', ['uses' => 'OAuthClientController@callback', 'as' => 'oauth_client_callback']);
Route::get('/oauth_client/logout/{provider}', 'OAuthClientController@logout');
Route::post('/oauth_client/remove/{provider}', ['uses' => 'OAuthClientController@remove', 'middleware' => 'auth']);
Route::get('/oauth_client/email_exists', 'OAuthClientController@emailExists');
Route::get('/lti', 'LTIController@login');

Route::get('/set_locale/{locale}', ['uses' => 'LocaleController@set', 'as' => 'set_locale']);

Route::group(['middleware' => ['auth']], function() {
    Route::get('/badge', 'BadgeController@index');
    Route::post('/badge/attach', 'BadgeController@attach');
    Route::post('/badge/do_not_have', 'BadgeController@doNotHave');
    Route::get('/profile', 'ProfileController@index');
    Route::post('/profile', 'ProfileController@update');
    Route::get('/account', ['uses' => 'AccountController@index', 'as' => 'account']);
    Route::post('/account/details', ['uses' => 'AccountController@updateAccount', 'as' => 'update_account']);
    Route::post('/account/password', ['uses' => 'AccountController@updatePassword', 'as' => 'update_password']);
});


Route::group(['prefix' => 'admin', 'middleware' => ['auth','admin'], 'namespace' => 'Admin'], function() {
    Route::get('/', 'DashboardController@index');
    Route::get('/users', 'UsersController@index');
    Route::get('/users/{id}/password', 'UsersController@showPassword');
    Route::post('/users/{id}/password', 'UsersController@updatePassword');
    Route::get('/users/{id}/emails', 'UsersController@showEmails');
    Route::post('/users/send_reset_link', 'UsersController@sendResetLink');
    Route::delete('/users/{id}', 'UsersController@delete');
});