<?php

Route::get('/oauth/authorize', '\App\Http\Controllers\Auth\AutoAuthorizationController@authorize');
Route::post('/oauth/authorize', 'ApproveAuthorizationController@approve');
Route::delete('/oauth/authorize', 'DenyAuthorizationController@deny');
