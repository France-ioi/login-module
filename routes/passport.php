<?php

Route::get('/oauth/authorize', 'AuthorizationController@authorize');
Route::post('/oauth/authorize', 'ApproveAuthorizationController@approve');
Route::delete('/oauth/authorize', 'DenyAuthorizationController@deny');