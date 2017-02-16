<?php

Auth::routes();
Route::get('/logout', 'Auth\LoginController@get_logout');

Route::get('/oauth_client/redirect/{provider}', 'OAuthClientController@redirect');
Route::get('/oauth_client/callback/{provider}', ['uses' => 'OAuthClientController@callback', 'as' => 'oauth_client_callback']);
Route::get('/oauth_client/logout/{provider}', 'OAuthClientController@logout');
Route::get('/lti', 'LTIController@login');

Route::get('/set_locale/{locale}', ['uses' => 'LocaleController@set', 'as' => 'set_locale']);

Route::group(['middleware' => ['auth']], function() {
    Route::get('/profile', 'ProfileController@index');
    Route::post('/profile', 'ProfileController@update');

    Route::get('/account', ['uses' => 'AccountController@index', 'as' => 'account']);
    Route::post('/account/details', ['uses' => 'AccountController@update_account', 'as' => 'update_account']);
    Route::post('/account/password', ['uses' => 'AccountController@update_password', 'as' => 'update_password']);
});


Route::group(['prefix' => 'admin', 'middleware' => ['auth','admin'], 'namespace' => 'Admin'], function() {
    Route::get('/', 'DashboardController@index');
    Route::get('/users', 'UsersController@index');
    Route::get('/users/{id}/password', 'UsersController@show_password');
    Route::post('/users/{id}/password', 'UsersController@update_password');
    Route::delete('/users/{id}', 'UsersController@delete');
});