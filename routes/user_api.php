<?php

Route::get('account', ['uses' => 'UserAPI\AccountController@show', 'middleware' => 'scopes:account']);