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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use GrahamCampbell\Binput\Facades\Binput;
use GrahamCampbell\Viewer\Facades\Viewer;
use GrahamCampbell\Queuing\Facades\Queuing;

/**
 * This is the registration controller class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/develop/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
class RegistrationController extends AbstractController
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
     * Display the registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        return Viewer::make(Config::get('credentials::register', 'credentials::account.register'));
    }

    /**
     * Attempt to register a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function postRegister()
    {
        if (!Config::get('credentials::regallowed')) {
            return Redirect::route('account.register');
        }

        $input = array(
            'first_name'            => Binput::get('first_name'),
            'last_name'             => Binput::get('last_name'),
            'email'                 => Binput::get('email'),
            'password'              => Binput::get('password'),
            'password_confirmation' => Binput::get('password_confirmation')
        );

        $rules = array (
            'first_name'            => 'required|min:2|max:32',
            'last_name'             => 'required|min:2|max:32',
            'email'                 => 'required|min:4|max:32|email',
            'password'              => 'required|min:6|confirmed',
            'password_confirmation' => 'required'
        );

        $val = Validator::make($input, $rules);
        if ($val->fails()) {
            Event::fire('user.registrationfailed', array(array('Email' => $input['email'], 'Messages' => $val->messages()->all())));
            return Redirect::route('account.register')->withErrors($val)->withInput();
        }

        try {
            unset($input['password_confirmation']);

            $user = Sentry::register($input);

            if (!Config::get('credentials::regemail')) {
                $user->attemptActivation($user->GetActivationCode());
                $user->addGroup(Sentry::getGroupProvider()->findByName('Users'));

                Event::fire('user.registrationsuccessful', array(array('Email' => $input['email'], 'Activated' => true)));
                Session::flash('success', 'Your account has been created successfully.');
                return Redirect::to(Config::get('credentials::home', '/'));
            }

            try {
                $data = array(
                    'view'    => 'emails.welcome',
                    'url'     => URL::to(Config::get('credentials::home', '/'));
                    'link'    => URL::route('account.activate', array('id' => $user->getId(), 'code' => $user->GetActivationCode())),
                    'email'   => $user->getLogin(),
                    'subject' => Config::get('platform.name').' - Welcome',
                );

                Queuing::pushMail($data);
            } catch (\Exception $e) {
                Log::alert($e);
                Event::fire('user.registrationfailed', array(array('Email' => $input['email'])));
                $user->delete();
                Session::flash('error', 'We were unable to create your account. Please contact support.');
                return Redirect::route('account.register')->withInput();
            }

            Event::fire('user.registrationsuccessful', array(array('Email' => $input['email'], 'Activated' => false)));
            Session::flash('success', 'Your account has been created. Check your email for the confirmation link.');
            return Redirect::to(Config::get('credentials::home', '/'));
        } catch (\Cartalyst\Sentry\Users\UserExistsException $e) {
            Log::notice($e);
            Event::fire('user.registrationfailed', array(array('Email' => $input['email'])));
            Session::flash('error', 'That email address is taken.');
            return Redirect::route('account.register')->withInput()->withErrors($val);
        }
    }

    /**
     * Activate an existing user.
     *
     * @param  int     $id
     * @param  string  $code
     * @return \Illuminate\Http\Response
     */
    public function getActivate($id, $code)
    {
        if (!$id || !$code) {
            App::abort(400);
        }

        try {
            $user = Sentry::getUserProvider()->findById($id);

            if (!$user->attemptActivation($code)) {
                Session::flash('error', 'There was a problem activating this account. Please contact support.');
                return Redirect::to(Config::get('credentials::home', '/'));
            }

            $user->addGroup(Sentry::getGroupProvider()->findByName('Users'));

            Event::fire('user.activationsuccessful', array(array('Email' => $user->email)));
            Session::flash('success', 'Your account has been activated successfully. You may now login.');
            return Redirect::route('account.login');
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Log::error($e);
            Event::fire('user.activationfailed');
            Session::flash('error', 'There was a problem activating this account. Please contact support.');
            return Redirect::to(Config::get('credentials::home', '/'));
        } catch (\Cartalyst\SEntry\Users\UserAlreadyActivatedException $e) {
            Log::notice($e);
            Event::fire('user.activationfailed', array(array('Email' => $user->email)));
            Session::flash('warning', 'You have already activated this account. You may want to login.');
            return Redirect::route('account.login');
        }
    }
}
