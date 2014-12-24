<?php

/*
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

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

// send users to the profile page
$router->get('account', ['as' => 'account', function () {
    Session::flash('', ''); // work around laravel bug if there is no session yet
    Session::reflash();

    return Redirect::route('account.profile');
});

// account routes
$router->get('account/history', ['as' => 'account.history', 'uses' => 'AccountController@getHistory']);
$router->get('account/profile', ['as' => 'account.profile', 'uses' => 'AccountController@getProfile']);
$router->delete('account/profile', ['as' => 'account.profile.delete', 'uses' => 'AccountController@deleteProfile']);
$router->patch('account/details', ['as' => 'account.details.patch', 'uses' => 'AccountController@patchDetails']);
$router->patch('account/password', ['as' => 'account.password.patch', 'uses' => 'AccountController@patchPassword']);

// registration routes
if (Config::get('graham-campbell/credentials::regallowed')) {
    $router->get('account/register', ['as' => 'account.register', 'uses' => 'RegistrationController@getRegister']);
    $router->post('account/register', ['as' => 'account.register.post', 'uses' => 'RegistrationController@postRegister']);
}

// activation routes
if (Config::get('graham-campbell/credentials::activation')) {
    $router->get('account/activate/{id}/{code}', ['as' => 'account.activate', 'uses' => 'ActivationController@getActivate']);
    $router->get('account/resend', ['as' => 'account.resend', 'uses' => 'ActivationController@getResend']);
    $router->post('account/resend', ['as' => 'account.resend.post', 'uses' => 'ActivationController@postResend']);
}

// reset routes
$router->get('account/reset', ['as' => 'account.reset', 'uses' => 'ResetController@getReset']);
$router->post('account/reset', ['as' => 'account.reset.post', 'uses' => 'ResetController@postReset']);
$router->get('account/password/{id}/{code}', ['as' => 'account.password', 'uses' => 'ResetController@getPassword']);

// login routes
$router->get('account/login', ['as' => 'account.login', 'uses' => 'LoginController@getLogin']);
$router->post('account/login', ['as' => 'account.login.post', 'uses' => 'LoginController@postLogin']);
$router->get('account/logout', ['as' => 'account.logout', 'uses' => 'LoginController@getLogout']);

// user routes
$router->resource('users', 'UserController');
$router->post('users/{users}/suspend', ['as' => 'users.suspend', 'uses' => 'UserController@suspend']);
$router->post('users/{users}/reset', ['as' => 'users.reset', 'uses' => 'UserController@reset']);
if (Config::get('graham-campbell/credentials::activation')) {
    $router->post('users/{users}/resend', ['as' => 'users.resend', 'uses' => 'UserController@resend']);
}
