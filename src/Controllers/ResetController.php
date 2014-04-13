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

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use GrahamCampbell\Binput\Facades\Binput;
use GrahamCampbell\Viewer\Facades\Viewer;
use GrahamCampbell\Queuing\Facades\Queuing;
use GrahamCampbell\Credentials\Classes\Credentials;
use GrahamCampbell\Credentials\Facades\UserProvider;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * This is the reset controller class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
class ResetController extends AbstractController
{
    /**
     * Create a new instance.
     *
     * @param  \GrahamCampbell\Credentials\Classes\Credentials  $credentials
     * @return void
     */
    public function __construct(Credentials $credentials)
    {
        $this->beforeFilter('throttle.reset', array('only' => array('postReset')));

        parent::__construct($credentials);
    }

    /**
     * Display the password reset form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getReset()
    {
        return Viewer::make(Config::get('graham-campbell/credentials::reset', 'graham-campbell/credentials::account.reset'));
    }

    /**
     * Queue the sending of the password reset email.
     *
     * @return \Illuminate\Http\Response
     */
    public function postReset()
    {
        $input = array(
            'email' => Binput::get('email'),
        );

        $val = UserProvider::validate($input, array_keys($input));
        if ($val->fails()) {
            return Redirect::route('account.reset')->withInput()->withErrors($val->errors());
        }

        try {
            $user = $this->credentials->getUserProvider()->findByLogin($input['email']);

            $data = array(
                'view' => 'graham-campbell/credentials::emails.reset',
                'link' => URL::route('account.password', array('id' => $user->id, 'code' => $user->getResetPasswordCode())),
                'email' => $user->getLogin(),
                'subject' => Config::get('platform.name').' - Password Reset Confirmation',
            );

            try {
                Queuing::pushMail($data);
            } catch (\Exception $e) {
                return Redirect::route('account.reset')->withInput()
                    ->with('error', 'We were unable to reset your password. Please contact support.');
            }

            return Redirect::route('account.reset')
                ->with('success', 'Check your email for password reset information.');
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            return Redirect::route('account.reset')
                ->with('error', 'That user does not exist.');
        }
    }

    /**
     * Reset the user's password.
     *
     * @param  int     $id
     * @param  string  $code
     * @return \Illuminate\Http\Response
     */
    public function getPassword($id, $code)
    {
        if (!$id || !$code) {
            throw new BadRequestHttpException();
        }

        try {
            $user = $this->credentials->getUserProvider()->findById($id);

            $password = Str::random();

            if (!$user->attemptResetPassword($code, $password)) {
                return Redirect::to(Config::get('graham-campbell/core::home', '/'))
                    ->with('error', 'There was a problem resetting your password. Please contact support.');
            }

            try {
                $data = array(
                    'view' => 'graham-campbell/credentials::emails.password',
                    'password' => $password,
                    'email' => $user->getLogin(),
                    'subject' => Config::get('platform.name').' - New Password Information',
                );

                Queuing::pushMail($data);
            } catch (\Exception $e) {
                return Redirect::to(Config::get('graham-campbell/core::home', '/'))
                    ->with('error', 'We were unable to send you your password. Please contact support.');
            }

            return Redirect::to(Config::get('graham-campbell/core::home', '/'))
                ->with('success', 'Your password has been changed. Check your email for the new password.');
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            return Redirect::to(Config::get('graham-campbell/core::home', '/'))
                ->with('error', 'There was a problem resetting your password. Please contact support.');
        }
    }
}
