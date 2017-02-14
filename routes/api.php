<?php

Route::get('account', ['uses' => 'API\AccountController@show', 'middleware' => 'scopes:account']);