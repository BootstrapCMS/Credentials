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

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use GrahamCampbell\Binput\Facades\Binput;
use GrahamCampbell\Viewer\Facades\Viewer;
use GrahamCampbell\Credentials\Classes\Credentials;
use GrahamCampbell\Credentials\Facades\UserProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This is the account controller class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
class AccountController extends AbstractController
{
    /**
     * Create a new instance.
     *
     * @param  \GrahamCampbell\Credentials\Classes\Credentials  $credentials
     * @return void
     */
    public function __construct(Credentials $credentials)
    {
        $this->setPermissions(array(
            'getProfile'    => 'user',
            'deleteProfile' => 'user',
            'patchDetails'  => 'user',
            'patchPassword' => 'user',
        ));

        parent::__construct($credentials);
    }

    /**
     * Display the user's profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProfile()
    {
        return Viewer::make(Config::get('graham-campbell/credentials::profile', 'graham-campbell/credentials::account.profile'));
    }

    /**
     * Delete the user's profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteProfile()
    {
        $user = $this->credentials->getUser();
        $this->checkUser($user);

        Event::fire('user.logout', array(array('Email' => $user->email)));
        $this->credentials->logout();

        try {
            $user->delete();
        } catch (\Exception $e) {
            Session::flash('error', 'There was a problem deleting your account.');
            return Redirect::to(Config::get('graham-campbell/core::home', '/'));
        }

        Session::flash('success', 'Your account has been deleted successfully.');
        return Redirect::to(Config::get('graham-campbell/core::home', '/'));
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

        $val = UserProvider::validate($input, array_keys($input));
        if ($val->fails()) {
            return Redirect::route('account.profile')->withInput()->withErrors($val->errors());
        }

        $user = $this->credentials->getUser();
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

        $val = UserProvider::validate($input, array_keys($input));
        if ($val->fails()) {
            return Redirect::route('account.profile')->withInput()->withErrors($val->errors());
        }

        unset($input['password_confirmation']);

        $user = $this->credentials->getUser();
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
            throw new NotFoundHttpException('User Not Found');
        }
    }
}
