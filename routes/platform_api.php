<?php

Route::group(['middleware' => ['platform_api']], function() {
    Route::post('accounts_manager/create', 'PlatformAPI\AccountsManagerController@create');
    Route::post('accounts_manager/delete', 'PlatformAPI\AccountsManagerController@delete');
    Route::post('badges_manager/reset_do_not_posess', 'PlatformAPI\BadgesManagerController@resetDoNotPosess');
});