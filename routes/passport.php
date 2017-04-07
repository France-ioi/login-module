<?php

Route::get('/oauth/authorize', ['uses' => 'AuthorizationController@authorize', 'middleware' => 'auto_authorization']);
Route::post('/oauth/authorize', 'ApproveAuthorizationController@approve');
Route::delete('/oauth/authorize', 'DenyAuthorizationController@deny');
Route::get('/oauth/auto_authorize', '\App\Http\Controllers\Auth\AutoAuthorizationController@authorize');
