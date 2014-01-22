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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use GrahamCampbell\Binput\Facades\Binput;
use GrahamCampbell\Viewer\Facades\Viewer;
use GrahamCampbell\Credentials\Facades\Credentials;

/**
 * This is the login controller class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
class LoginController extends AbstractController
{
    /**
     * Constructor (setup access permissions).
     *
     * @return void
     */
    public function __construct()
    {
        $this->setPermissions(array(
            'getLogout' => 'user',
        ));

        $this->beforeFilter('throttle.login', array('only' => array('postLogin')));

        parent::__construct();
    }

    /**
     * Display the login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        return Viewer::make(Config::get('credentials::login', 'credentials::account.login'));
    }

    /**
     * Attempt to login the specified user.
     *
     * @return \Illuminate\Http\Response
     */
    public function postLogin()
    {
        $remember = Binput::get('rememberMe');

        $input = array(
            'email'    => Binput::get('email'),
            'password' => Binput::get('password'),
        );

        $rules = array(
            'email'    => 'required|min:4|max:32|email',
            'password' => 'required|min:6',
        );

        $val = Validator::make($input, $rules);
        if ($val->fails()) {
            Event::fire('user.loginfailed', array(array('Email' => $input['email'], 'Messages' => $val->messages()->all())));
            return Redirect::route('account.login')->withInput()->withErrors($val);
        }

        try {
            $throttle = Credentials::getThrottleProvider()->findByUserLogin($input['email']);
            $throttle->check();

            Credentials::authenticate($input, $remember);
        } catch (\Cartalyst\Sentry\Users\WrongPasswordException $e) {
            Log::notice($e);
            Event::fire('user.loginfailed', array(array('Email' => $input['email'])));
            Session::flash('error', 'Your password was incorrect.');
            return Redirect::route('account.login')->withErrors($val)->withInput();
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Log::notice($e);
            Event::fire('user.loginfailed', array(array('Email' => $input['email'])));
            Session::flash('error', 'That user does not exist.');
            return Redirect::route('account.login')->withErrors($val)->withInput();
        } catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e) {
            Log::notice($e);
            Event::fire('user.loginfailed', array(array('Email' => $input['email'])));
            Session::flash('error', 'You have not yet activated this account.');
            return Redirect::route('account.login')->withErrors($val)->withInput();
        } catch (\Cartalyst\Sentry\Throttling\UserSuspendedException $e) {
            Log::notice($e);
            Event::fire('user.loginfailed', array(array('Email' => $input['email'])));
            $time = $throttle->getSuspensionTime();
            Session::flash('error', "Your account has been suspended for $time minutes.");
            return Redirect::route('account.login')->withErrors($val)->withInput();
        } catch (\Cartalyst\Sentry\Throttling\UserBannedException $e) {
            Log::notice($e);
            Event::fire('user.loginfailed', array(array('Email' => $input['email'])));
            Session::flash('error', 'You have been banned. Please contact support.');
            return Redirect::route('account.login')->withErrors($val)->withInput();
        }

        Event::fire('user.loginsuccessful', array(array('Email' => $input['email'])));
        return Redirect::intended(Config::get('credentials::home', '/'));
    }

    /**
     * Logout the specified user.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        Event::fire('user.logout', array(array('Email' => Credentials::getUser()->email)));
        Credentials::logout();
        return Redirect::to(Config::get('credentials::home', '/'));
    }
}
