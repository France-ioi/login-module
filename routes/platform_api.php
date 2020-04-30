<?php

Route::group(['middleware' => ['platform_api']], function() {
    Route::post('accounts_manager/create', 'PlatformAPI\AccountsManagerController@create');
    Route::post('accounts_manager/delete', 'PlatformAPI\AccountsManagerController@delete');
    Route::post('accounts_manager/unlink_client', 'PlatformAPI\AccountsManagerController@unlinkClient');
    Route::post('accounts_manager/participation_codes', 'PlatformAPI\AccountsManagerController@participationCodes');
    Route::post('badges_manager/reset_do_not_possess', 'PlatformAPI\BadgesManagerController@resetDoNotPossess');

    Route::post('lti/entry', 'PlatformAPI\LtiInterfaceController@entry');
    Route::post('lti/send_result', 'PlatformAPI\LtiInterfaceController@sendResult');

    Route::post('lti_result/send', 'PlatformAPI\LtiResultDispatcherController@sendResult');
});
