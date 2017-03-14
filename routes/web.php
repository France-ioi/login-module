<?php

Route::get('/', function() { return redirect('login'); });
Auth::routes();
Route::get('/logout', 'Auth\LogoutController@getLogout');
Route::post('/logout_start', 'Auth\LogoutController@logoutStart');
Route::get('/logout_loop', 'Auth\LogoutController@logoutLoop');
Route::get('/logout_finish', 'Auth\LogoutController@logoutFinish');
Route::get('/login_email', 'Auth\LoginController@showLoginEmailForm');
Route::post('/badge/verify', 'BadgeController@verify');
Route::get('/email_verification', 'EmailVerificationController@index');
Route::post('/email_verification/send', 'EmailVerificationController@sendVerificationLink');
Route::get('/email_verification/verify/{token}', ['uses' => 'EmailVerificationController@verifyEmail', 'as' => 'email_verification']);

Route::get('/oauth_client/redirect/{provider}', 'Auth\OAuthClientController@redirect');
Route::get('/oauth_client/callback/{provider}', ['uses' => 'Auth\OAuthClientController@callback', 'as' => 'oauth_client_callback']);
Route::post('/oauth_client/remove/{provider}', ['uses' => 'Auth\OAuthClientController@remove', 'middleware' => 'auth']);
Route::get('/oauth_client/email_exists', 'Auth\OAuthClientController@emailExists');
Route::get('/oauth_client/logout/{provider}', 'Auth\OAuthClientController@logout');
Route::get('/lti', 'Auth\LTIController@login');

Route::get('/set_locale/{locale}', ['uses' => 'LocaleController@set', 'as' => 'set_locale']);

Route::group(['middleware' => ['auth']], function() {
    Route::get('/badge', 'BadgeController@index');
    Route::post('/badge/attach', 'BadgeController@attach');
    Route::post('/badge/do_not_have', 'BadgeController@doNotHave');
    Route::get('/profile', 'ProfileController@index');
    Route::post('/profile', 'ProfileController@update');

    Route::get('/account', ['uses' => 'AccountController@index', 'as' => 'account']);
    Route::post('/account/details', ['uses' => 'AccountController@updateAccount', 'as' => 'update_account']);


    Route::get('/auth_connections', 'AuthConnectionsController@index');
    Route::get('/password', 'PasswordController@index');
    Route::post('/password', 'PasswordController@updatePassword');
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