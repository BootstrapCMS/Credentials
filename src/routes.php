<?php

/**
 * This file is part of Laravel Credentials by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at http://bit.ly/UWsjkb.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

// send users to the profile page
Route::get('account', ['as' => 'account', function () {
    Session::flash('', ''); // work around laravel bug if there is no session yet
    Session::reflash();
    return Redirect::route('account.profile');
}, ]);

// account routes
Route::get('account/history', [
    'as' => 'account.history',
    'uses' => 'GrahamCampbell\Credentials\Http\Controllers\AccountController@getHistory',
]);
Route::get('account/profile', [
    'as' => 'account.profile',
    'uses' => 'GrahamCampbell\Credentials\Http\Controllers\AccountController@getProfile',
]);
Route::delete('account/profile', [
    'as' => 'account.profile.delete',
    'uses' => 'GrahamCampbell\Credentials\Http\Controllers\AccountController@deleteProfile',
]);
Route::patch('account/details', [
    'as' => 'account.details.patch',
    'uses' => 'GrahamCampbell\Credentials\Http\Controllers\AccountController@patchDetails',
]);
Route::patch('account/password', [
    'as' => 'account.password.patch',
    'uses' => 'GrahamCampbell\Credentials\Http\Controllers\AccountController@patchPassword',
]);

// registration routes
if (Config::get('graham-campbell/credentials::regallowed')) {
    Route::get('account/register', [
        'as' => 'account.register',
        'uses' => 'GrahamCampbell\Credentials\Http\Controllers\RegistrationController@getRegister',
    ]);
    Route::post('account/register', [
        'as' => 'account.register.post',
        'uses' => 'GrahamCampbell\Credentials\Http\Controllers\RegistrationController@postRegister',
    ]);
}

// activation routes
if (Config::get('graham-campbell/credentials::activation')) {
    Route::get('account/activate/{id}/{code}', [
        'as' => 'account.activate',
        'uses' => 'GrahamCampbell\Credentials\Http\Controllers\ActivationController@getActivate',
    ]);
    Route::get('account/resend', [
        'as' => 'account.resend',
        'uses' => 'GrahamCampbell\Credentials\Http\Controllers\ActivationController@getResend',
    ]);
    Route::post('account/resend', [
        'as' => 'account.resend.post',
        'uses' => 'GrahamCampbell\Credentials\Http\Controllers\ActivationController@postResend',
    ]);
}

// reset routes
Route::get('account/reset', [
    'as' => 'account.reset',
    'uses' => 'GrahamCampbell\Credentials\Http\Controllers\ResetController@getReset',
]);
Route::post('account/reset', [
    'as' => 'account.reset.post',
    'uses' => 'GrahamCampbell\Credentials\Http\Controllers\ResetController@postReset',
]);
Route::get('account/password/{id}/{code}', [
    'as' => 'account.password',
    'uses' => 'GrahamCampbell\Credentials\Http\Controllers\ResetController@getPassword',
]);

// login routes
Route::get('account/login', [
    'as' => 'account.login',
    'uses' => 'GrahamCampbell\Credentials\Http\Controllers\LoginController@getLogin',
]);
Route::post('account/login', [
    'as' => 'account.login.post',
    'uses' => 'GrahamCampbell\Credentials\Http\Controllers\LoginController@postLogin',
]);
Route::get('account/logout', [
    'as' => 'account.logout',
    'uses' => 'GrahamCampbell\Credentials\Http\Controllers\LoginController@getLogout',
]);

// user routes
Route::resource('users', 'GrahamCampbell\Credentials\Http\Controllers\UserController');
Route::post('users/{users}/suspend', [
    'as' => 'users.suspend',
    'uses' => 'GrahamCampbell\Credentials\Http\Controllers\UserController@suspend',
]);
Route::post('users/{users}/reset', [
    'as' => 'users.reset',
    'uses' => 'GrahamCampbell\Credentials\Http\Controllers\UserController@reset',
]);
if (Config::get('graham-campbell/credentials::activation')) {
    Route::post('users/{users}/resend', [
        'as' => 'users.resend',
        'uses' => 'GrahamCampbell\Credentials\Http\Controllers\UserController@resend',
    ]);
}
