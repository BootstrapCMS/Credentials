<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// send users to the profile page
Route::get('account', ['as' => 'account', function () {
    Session::flash('', ''); // work around laravel bug if there is no session yet
    Session::reflash();

    return Redirect::route('account.profile');
}]);

// account routes
Route::get('account/history', [
    'as'   => 'account.history',
    'uses' => 'GrahamCampbell\Credentials\Controllers\AccountController@getHistory',
]);
Route::get('account/profile', [
    'as'   => 'account.profile',
    'uses' => 'GrahamCampbell\Credentials\Controllers\AccountController@getProfile',
]);
Route::delete('account/profile', [
    'as'   => 'account.profile.delete',
    'uses' => 'GrahamCampbell\Credentials\Controllers\AccountController@deleteProfile',
]);
Route::patch('account/details', [
    'as'   => 'account.details.patch',
    'uses' => 'GrahamCampbell\Credentials\Controllers\AccountController@patchDetails',
]);
Route::patch('account/password', [
    'as'   => 'account.password.patch',
    'uses' => 'GrahamCampbell\Credentials\Controllers\AccountController@patchPassword',
]);

// registration routes
if (Config::get('graham-campbell/credentials::regallowed')) {
    Route::get('account/register', [
        'as'   => 'account.register',
        'uses' => 'GrahamCampbell\Credentials\Controllers\RegistrationController@getRegister',
    ]);
    Route::post('account/register', [
        'as'   => 'account.register.post',
        'uses' => 'GrahamCampbell\Credentials\Controllers\RegistrationController@postRegister',
    ]);
}

// activation routes
if (Config::get('graham-campbell/credentials::activation')) {
    Route::get('account/activate/{id}/{code}', [
        'as'   => 'account.activate',
        'uses' => 'GrahamCampbell\Credentials\Controllers\ActivationController@getActivate',
    ]);
    Route::get('account/resend', [
        'as'   => 'account.resend',
        'uses' => 'GrahamCampbell\Credentials\Controllers\ActivationController@getResend',
    ]);
    Route::post('account/resend', [
        'as'   => 'account.resend.post',
        'uses' => 'GrahamCampbell\Credentials\Controllers\ActivationController@postResend',
    ]);
}

// reset routes
Route::get('account/reset', [
    'as'   => 'account.reset',
    'uses' => 'GrahamCampbell\Credentials\Controllers\ResetController@getReset',
]);
Route::post('account/reset', [
    'as'   => 'account.reset.post',
    'uses' => 'GrahamCampbell\Credentials\Controllers\ResetController@postReset',
]);
Route::get('account/password/{id}/{code}', [
    'as'   => 'account.password',
    'uses' => 'GrahamCampbell\Credentials\Controllers\ResetController@getPassword',
]);

// login routes
Route::get('account/login', [
    'as'   => 'account.login',
    'uses' => 'GrahamCampbell\Credentials\Controllers\LoginController@getLogin',
]);
Route::post('account/login', [
    'as'   => 'account.login.post',
    'uses' => 'GrahamCampbell\Credentials\Controllers\LoginController@postLogin',
]);
Route::get('account/logout', [
    'as'   => 'account.logout',
    'uses' => 'GrahamCampbell\Credentials\Controllers\LoginController@getLogout',
]);

// user routes
Route::resource('users', 'GrahamCampbell\Credentials\Controllers\UserController');
Route::post('users/{users}/suspend', [
    'as'   => 'users.suspend',
    'uses' => 'GrahamCampbell\Credentials\Controllers\UserController@suspend',
]);
Route::post('users/{users}/reset', [
    'as'   => 'users.reset',
    'uses' => 'GrahamCampbell\Credentials\Controllers\UserController@reset',
]);
if (Config::get('graham-campbell/credentials::activation')) {
    Route::post('users/{users}/resend', [
        'as'   => 'users.resend',
        'uses' => 'GrahamCampbell\Credentials\Controllers\UserController@resend',
    ]);
}
