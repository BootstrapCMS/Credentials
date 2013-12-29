<?php

/**
 * This file is part of Laravel Credentials by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */


// send users to the profile page
Route::get('account', array('as' => 'account', function () {
    Session::flash('', ''); // work around laravel bug if there is no session yet
    Session::reflash();
    return Redirect::route('account.profile');
}));


// account routes
Route::get('account/profile', array('as' => 'account.profile', 'uses' => 'GrahamCampbell\BootstrapCMS\Controllers\AccountController@getProfile'));
Route::delete('account/profile', array('as' => 'account.profile.delete', 'uses' => 'GrahamCampbell\BootstrapCMS\Controllers\AccountController@deleteProfile'));
Route::patch('account/details', array('as' => 'account.details.patch', 'uses' => 'GrahamCampbell\BootstrapCMS\Controllers\AccountController@patchDetails'));
Route::patch('account/password', array('as' => 'account.password.patch', 'uses' => 'GrahamCampbell\BootstrapCMS\Controllers\AccountController@patchPassword'));


// login routes
Route::get('account/login', array('as' => 'account.login', 'uses' => 'GrahamCampbell\BootstrapCMS\Controllers\LoginController@getLogin'));
Route::post('account/login', array('as' => 'account.login.post', 'uses' => 'GrahamCampbell\BootstrapCMS\Controllers\LoginController@postLogin'));
Route::get('account/logout', array('as' => 'account.logout', 'uses' => 'GrahamCampbell\BootstrapCMS\Controllers\LoginController@getLogout'));


// reset routes
Route::get('account/reset', array('as' => 'account.reset', 'uses' => 'GrahamCampbell\BootstrapCMS\Controllers\ResetController@getReset'));
Route::post('account/reset', array('as' => 'account.reset.post', 'uses' => 'GrahamCampbell\BootstrapCMS\Controllers\ResetController@postReset'));
Route::get('account/password/{id}/{code}', array('as' => 'account.password', 'uses' => 'GrahamCampbell\BootstrapCMS\Controllers\ResetController@getPassword'));


// registration routes
if (Config::get('credentials::regallowed')) {
    Route::get('account/register', array('as' => 'account.register', 'uses' => 'GrahamCampbell\BootstrapCMS\Controllers\RegistrationController@getRegister'));
    Route::post('account/register', array('as' => 'account.register.post', 'uses' => 'GrahamCampbell\BootstrapCMS\Controllers\RegistrationController@postRegister'));
}


// activation route
Route::get('account/activate/{id}/{code}', array('as' => 'account.activate', 'uses' => 'GrahamCampbell\BootstrapCMS\Controllers\RegistrationController@getActivate'));


// user routes
Route::resource('users', 'GrahamCampbell\BootstrapCMS\Controllers\UserController');
Route::post('users/{users}/suspend', array('as' => 'users.suspend', 'uses' => 'GrahamCampbell\BootstrapCMS\Controllers\UserController@suspend'));
Route::post('users/{users}/reset', array('as' => 'users.reset', 'uses' => 'GrahamCampbell\BootstrapCMS\Controllers\UserController@reset'));
