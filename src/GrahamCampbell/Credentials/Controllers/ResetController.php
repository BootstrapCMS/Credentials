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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use GrahamCampbell\Binput\Facades\Binput;
use GrahamCampbell\Passwd\Facades\Passwd;
use GrahamCampbell\Viewer\Facades\Viewer;
use GrahamCampbell\Queuing\Facades\Queuing;

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
     * Constructor (setup access permissions).
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display the password reset form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getReset()
    {
        return Viewer::make(Config::get('credentials::reset', 'credentials::account.reset'));
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

        $rules = array (
            'email' => 'required|min:4|max:32|email',
        );

        $val = Validator::make($input, $rules);
        if ($val->fails()) {
            return Redirect::route('account.reset')->withErrors($val)->withInput();
        }

        try {
            $user = Sentry::getUserProvider()->findByLogin($input['email']);

            $data = array(
                'view' => 'credentials::emails.reset',
                'link' => URL::route('account.password', array('id' => $user->getId(), 'code' => $user->getResetPasswordCode())),
                'email' => $user->getLogin(),
                'subject' => Config::get('platform.name').' - Password Reset Confirmation',
            );

            try {
                Queuing::pushMail($data);
            } catch (\Exception $e) {
                Log::alert($e);
                Session::flash('error', 'We were unable to reset your password. Please contact support.');
                return Redirect::route('account.reset')->withInput();
            }

            Log::info('Reset email sent', array('Email' => $input['email']));
            Session::flash('success', 'Check your email for password reset information.');
            return Redirect::route('account.reset');
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Log::notice($e);
            Session::flash('error', 'That user does not exist.');
            return Redirect::route('account.reset');
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
            App::abort(400);
        }

        try {
            $user = Sentry::getUserProvider()->findById($id);

            $password = Passwd::generate();

            if (!$user->attemptResetPassword($code, $password)) {
                Log::error('There was a problem resetting a password', array('Id' => $id));
                Session::flash('error', 'There was a problem resetting your password. Please contact support.');
                return Redirect::to(Config::get('credentials::home', '/'));
            }

            try {
                $data = array(
                    'view' => 'credentials::emails.password',
                    'password' => $password,
                    'email' => $user->getLogin(),
                    'subject' => Config::get('platform.name').' - New Password Information',
                );

                Queuing::pushMail($data);
            } catch (\Exception $e) {
                Log::alert($e);
                Session::flash('error', 'We were unable to send you your password. Please contact support.');
                return Redirect::to(Config::get('credentials::home', '/'));
            }

            Log::info('Password reset successfully', array('Email' => $data['email']));
            Session::flash('success', 'Your password has been changed. Check your email for the new password.');
            return Redirect::to(Config::get('credentials::home', '/'));
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Log::error($e);
            Session::flash('error', 'There was a problem resetting your password. Please contact support.');
            return Redirect::to(Config::get('credentials::home', '/'));
        }
    }
}
