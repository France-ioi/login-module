<?php

Route::redirect('/', '/auth');

// Auth
Route::get('/password/reset/new', ['uses' => 'Auth\ResetPasswordController@showNewPasswordForm', 'as' => 'password.reset.new']);
Auth::routes();
Route::get('/password/emails/{login}', ['uses' => 'Auth\ForgotPasswordController@showEmails', 'as' => 'passwords.emails']);
Route::post('/logout', 'Auth\LogoutController@logoutFinish');
Route::post('/login_with_code', 'Auth\LoginWithCodeController@login');
Route::get('/auth', 'Auth\IndexController@index');
Route::get('/logout', 'Auth\LogoutController@getLogout');
Route::post('/logout_start', 'Auth\LogoutController@logoutStart');
Route::get('/logout_loop', 'Auth\LogoutController@logoutLoop');
Route::get('/logout_finish', 'Auth\LogoutController@logoutFinish');
Route::get('/session_expired', 'Auth\OAuthClientController@sessionExpired');

// OAuth
Route::get('/oauth_client/redirect/{provider}', 'Auth\OAuthClientController@redirect');
Route::get('/oauth_client/preferences/{provider}', 'Auth\OAuthClientController@preferences');
Route::get('/oauth_client/callback/{provider}', ['uses' => 'Auth\OAuthClientController@callback', 'as' => 'oauth_client_callback']);
Route::post('/oauth_client/remove/{provider}', ['uses' => 'Auth\OAuthClientController@remove', 'middleware' => 'auth']);
Route::get('/oauth_client/email_exists', 'Auth\OAuthClientController@emailExists');
Route::get('/oauth_client/logout/{provider}', 'Auth\OAuthClientController@logout');

// LTI
Route::post('/lti/entry', 'Auth\LTIEntryController@handle');
Route::post('/lti/launch', 'Auth\LTILaunchController@handle');
Route::get('/lti', 'Auth\LTIController@login');

// Misc
Route::get('/set_locale/{locale}', ['uses' => 'LocaleController@set', 'as' => 'set_locale']);

// Logged user
Route::group(['middleware' => ['auth']], function() {
    Route::get('/ban', 'BanController@index');

    Route::get('/badges', 'BadgesController@index');
    Route::post('/badges/add', 'BadgesController@add');

    Route::get('/participation_code', 'ParticipationCodeController@index');
    Route::get('/merging_accounts', 'MergingAccountsController@index');
    Route::post('/merging_accounts/accept', 'MergingAccountsController@acceptMerge');
    Route::post('/merging_accounts/decline', 'MergingAccountsController@declineMerge');

    Route::get('/collected_data/summary', 'CollectedDataController@summary');
    Route::get('/collected_data/export', 'CollectedDataController@export');
    Route::get('/collected_data', 'CollectedDataController@index');
    Route::post('/collected_data/delete', 'CollectedDataController@delete');

    Route::get('/verification', 'Verification\IndexController@index');
    Route::get('/verification/select_method/{attribute}', 'Verification\IndexController@selectMethod')->name('verification/select_method');;
    Route::post('/verification/delete/{id}', 'Verification\IndexController@delete');
    Route::get('/verification/email_code', 'Verification\Methods\EmailCodeController@index');
    Route::post('/verification/email_code', 'Verification\Methods\EmailCodeController@store');
    Route::get('/verification/email_domain', 'Verification\Methods\EmailDomainController@index');
    Route::post('/verification/email_domain', 'Verification\Methods\EmailDomainController@sendCode');
    Route::get('/verification/email_domain/input_code/{id}', 'Verification\Methods\EmailDomainController@showInputCode');
    Route::post('/verification/email_domain/validate_code/{id}', 'Verification\Methods\EmailDomainController@validateCode');
    Route::get('/verification/id_upload', 'Verification\Methods\IdUploadController@index');
    Route::post('/verification/id_upload', 'Verification\Methods\IdUploadController@store');
    Route::get('/verification/classroom_upload', 'Verification\Methods\ClassroomUploadController@index');
    Route::post('/verification/classroom_upload', 'Verification\Methods\ClassroomUploadController@store');
    Route::get('/verification/peer', 'Verification\Methods\PeerValidationController@index');
    Route::post('/verification/peer', 'Verification\Methods\PeerValidationController@store');
    Route::get('/verification/peer_code/{id}', 'Verification\Methods\PeerValidationController@code');
    Route::post('/verification/peer_code/{id}', 'Verification\Methods\PeerValidationController@storeCode');
    Route::get('/verification/imported_data/{id}', 'Verification\Methods\ImportedDataController@index');
    Route::get('/verification/manual', 'Verification\Methods\ManualController@index');
    Route::get('/verification/imported_data/{id}', 'Verification\Methods\ImportedDataController@index');    

    Route::group(['middleware' => ['merging_accounts']], function() {
        Route::get('/badge', 'BadgeController@index');
        Route::post('/badge/attach', 'BadgeController@attach');
        Route::post('/badge/do_not_have', 'BadgeController@doNotHave');
        Route::get('/badge/confirm_difference', 'BadgeController@getConfirmDifference');
        Route::post('/badge/confirm_difference', 'BadgeController@confirmDifference');
        Route::get('/profile', 'ProfileController@index');
        Route::post('/profile', 'ProfileController@update');
        Route::get('/official_domains', 'OfficialDomainsController@index');
        Route::get('/timezone', 'TimezoneController@index');
        Route::get('/reauthentication', 'Auth\ReauthenticationController@index');
        Route::post('/reauthentication', 'Auth\ReauthenticationController@update');
        Route::group(['middleware' => ['reauthentication']], function() {
            Route::get('/auth_methods', 'AuthMethodsController@index');
            Route::post('/auth_methods/badge_login_ability/{id}/{enabled}', 'AuthMethodsController@setBadgeLoginAbility');
            Route::get('/password', 'PasswordController@index');
            Route::post('/password', 'PasswordController@updatePassword');
        });
    });

    // Client admin
    Route::group(['prefix' => 'client_admin/{client_id}', 'namespace' => 'ClientAdmin'], function() {
        Route::get('/users', 'UsersController@index');
        Route::get('/users/{user_id}/verification', 'UsersController@showVerification');
        Route::post('/users/{user_id}/verification', 'UsersController@updateVerification');
        Route::get('/users/{user_id}/ban', 'UsersController@showBan');
        Route::post('/users/{user_id}/ban', 'UsersController@updateBan');        
    });    
});



// Admin
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'role:admin'], 'namespace' => 'Admin'], function() {
    Route::get('/', 'DashboardController@index');
    Route::group(['middleware' => 'permission:admin.users.manager'], function() {
        Route::get('/users', 'UsersController@index');
        Route::get('/users/{id}', 'UsersController@show');
        Route::get('/users/{id}/password', 'UsersController@showPassword');
        Route::post('/users/{id}/password', 'UsersController@updatePassword');
        Route::get('/users/{id}/permissions', 'UsersController@showPermissions');
        Route::post('/users/{id}/permissions', 'UsersController@updatePermissions');
        Route::get('/users/{id}/emails', 'UsersController@showEmails');
        Route::post('/users/create_reset_link', 'UsersController@createResetLink');
        Route::post('/users/send_reset_link', 'UsersController@sendResetLink');
        Route::delete('/users/{id}', 'UsersController@delete');
        Route::get('/users/{id}/clients', 'UsersController@showClients');
        Route::post('/users/{id}/clients', 'UsersController@updateClients');
        Route::get('/reset_passwords', 'ResetPasswordsController@index');
        Route::post('/reset_passwords', 'ResetPasswordsController@reset');
    });
    Route::group(['middleware' => 'permission:admin.clients.manager'], function() {
        Route::resource('clients', 'ClientsController');
    });
    Route::group(['middleware' => 'permission:admin.domains.manager'], function() {
        Route::resource('official_domains', 'OfficialDomainsController');
    });
    Route::group(['middleware' => 'permission:admin.lm_instances.manager'], function() {
        Route::resource('origin_instances', 'OriginInstancesController');
    });
    Route::group(['middleware' => 'permission:admin.verifications.manager'], function() {
        //Route::get('/verifications', 'VerificationsController@index');
    });
    Route::group(['middleware' => 'permission:admin.verifications.verify'], function() {
        Route::get('/verifications/edit', 'VerificationsController@edit');
        Route::post('/verifications/update/{id}', 'VerificationsController@update');
    });
    Route::group(['middleware' => 'permission:admin.misc'], function() {
        Route::resource('lti_configs', 'LtiConfigsController');
        Route::resource('badge_apis', 'BadgeApisController');
    });
});