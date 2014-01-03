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

namespace GrahamCampbell\Credentials\Controllers;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use GrahamCampbell\Binput\Facades\Binput;
use GrahamCampbell\Viewer\Facades\Viewer;
use GrahamCampbell\CMSCore\Models\Page;

/**
 * This is the account controller class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/develop/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
class AccountController extends AbstractController
{
    /**
     * Constructor (setup access permissions).
     *
     * @return void
     */
    public function __construct()
    {
        $this->setPermissions(array(
            'getProfile'    => 'user',
            'deleteProfile' => 'user',
            'patchDetails'  => 'user',
            'patchPassword' => 'user',
        ));

        parent::__construct();
    }

    /**
     * Display the user's profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProfile()
    {
        return Viewer::make(Config::get('credentials::profile', 'credentials::account.profile'));
    }

    /**
     * Delete the user's profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteProfile()
    {
        $user = Sentry::getUser();
        $this->checkUser($user);

        Event::fire('user.logout', array(array('Email' => $user->email)));
        Sentry::logout();

        $user->delete();

        Session::flash('success', 'Your account has been deleted successfully.');
        return Redirect::to(Config::get('credentials::home', '/'));
    }

    /**
     * Update the user's details.
     *
     * @return \Illuminate\Http\Response
     */
    public function patchDetails()
    {
        $input = array(
            'first_name' => Binput::get('first_name'),
            'last_name'  => Binput::get('last_name'),
            'email'      => Binput::get('email'),
        );

        $rules = array (
            'first_name' => 'required|min:2|max:32',
            'last_name'  => 'required|min:2|max:32',
            'email'      => 'required|min:4|max:32|email',
        );

        $val = Validator::make($input, $rules);

        $val = Validator::make($input, $rules);
        if ($val->fails()) {
            return Redirect::route('account.profile')->withInput()->withErrors($val->errors());
        }

        $user = Sentry::getUser();
        $this->checkUser($user);

        $user->update($input);
        
        Session::flash('success', 'Your details have been updated successfully.');
        return Redirect::route('account.profile');
    }

    /**
     * Update the user's password.
     *
     * @return \Illuminate\Http\Response
     */
    public function patchPassword()
    {
        $input = array(
            'password'              => Binput::get('password'),
            'password_confirmation' => Binput::get('password_confirmation'),
        );

        $rules = array (
            'password'              => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        );

        $val = Validator::make($input, $rules);
        if ($val->fails()) {
            return Redirect::route('account.profile')->withInput()->withErrors($val->errors());
        }

        unset($input['password_confirmation']);

        $user = Sentry::getUser();
        $this->checkUser($user);

        $user->update($input);
        
        Session::flash('success', 'Your password has been updated successfully.');
        return Redirect::route('account.profile');
    }

    /**
     * Check the user model.
     *
     * @param  mixed  $user
     * @return void
     */
    protected function checkUser($user)
    {
        if (!$user) {
            return App::abort(404, 'User Not Found');
        }
    }
}
