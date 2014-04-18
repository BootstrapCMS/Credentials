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
use GrahamCampbell\Binput\Classes\Binput;
use GrahamCampbell\Viewer\Classes\Viewer;
use GrahamCampbell\Credentials\Classes\Credentials;
use GrahamCampbell\Credentials\Providers\UserProvider;

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
     * The viewer instance.
     *
     * @var \GrahamCampbell\Viewer\Classes\Viewer
     */
    protected $viewer;

    /**
     * The binput instance.
     *
     * @var \GrahamCampbell\Binput\Classes\Binput
     */
    protected $binput;

    /**
     * The user provider instance.
     *
     * @var \GrahamCampbell\Credentials\Providers\UserProvider
     */
    protected $userprovider;

    /**
     * Create a new instance.
     *
     * @param  \GrahamCampbell\Credentials\Classes\Credentials  $credentials
     * @param  \GrahamCampbell\Viewer\Classes\Viewer  $viewer
     * @param  \GrahamCampbell\Binput\Classes\Binput  $binput
     * @param  \GrahamCampbell\Credentials\Providers\UserProvider  $userprovider
     * @return void
     */
    public function __construct(Credentials $credentials, Viewer $viewer, Binput $binput, UserProvider $userprovider)
    {
        $this->viewer = $viewer;
        $this->binput = $binput;
        $this->userprovider = $userprovider;

        $this->setPermissions(array(
            'getLogout' => 'user',
        ));

        $this->beforeFilter('throttle.login', array('only' => array('postLogin')));
        $this->beforeFilter('throttle.sentry', array('only' => array('postLogin')));

        parent::__construct($credentials);
    }

    /**
     * Display the login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        return $this->viewer->make(Config::get('graham-campbell/credentials::login', 'graham-campbell/credentials::account.login'));
    }

    /**
     * Attempt to login the specified user.
     *
     * @return \Illuminate\Http\Response
     */
    public function postLogin()
    {
        $remember = $this->binput->get('rememberMe');

        $input = $this->binput->only(array('email', 'password'));

        $rules = $this->userprovider->rules(array_keys($input));
        $rules['password'] = 'required|min:6';

        $val = $this->userprovider->validate($input, $rules, true);
        if ($val->fails()) {
            Event::fire('user.loginfailed', array(array('Email' => $input['email'], 'Messages' => $val->messages()->all())));
            return Redirect::route('account.login')->withInput()->withErrors($val->errors());
        }

        try {
            $throttle = $this->credentials->getThrottleProvider()->findByUserLogin($input['email']);
            $throttle->check();

            $this->credentials->authenticate($input, $remember);
        } catch (\Cartalyst\Sentry\Users\WrongPasswordException $e) {
            Event::fire('user.loginfailed', array(array('Email' => $input['email'])));
            return Redirect::route('account.login')->withInput()->withErrors($val->errors())
                ->with('error', 'Your password was incorrect.');
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Event::fire('user.loginfailed', array(array('Email' => $input['email'])));
            return Redirect::route('account.login')->withInput()->withErrors($val->errors())
                ->with('error', 'That user does not exist.');
        } catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e) {
            if (Config::get('graham-campbell/credentials::regemail')) {
                Event::fire('user.loginfailed', array(array('Email' => $input['email'])));
                return Redirect::route('account.login')->withInput()->withErrors($val->errors())
                ->with('error', 'You have not yet activated this account.');
            }
            $throttle->user()->attemptActivation($user->getActivationCode());
            $throttle->user()->addGroup($this->credentials->getGroupProvider()->findByName('Users'));
            $this->credentials->authenticate($input, $remember);
            Event::fire('user.loginsuccessful', array(array('Email' => $input['email'])));
            return Redirect::intended(Config::get('graham-campbell/core::home', '/'));
        } catch (\Cartalyst\Sentry\Throttling\UserSuspendedException $e) {
            Event::fire('user.loginfailed', array(array('Email' => $input['email'])));
            $time = $throttle->getSuspensionTime();
            return Redirect::route('account.login')->withInput()->withErrors($val->errors())
                ->with('error', "Your account has been suspended for $time minutes.");
        } catch (\Cartalyst\Sentry\Throttling\UserBannedException $e) {
            Event::fire('user.loginfailed', array(array('Email' => $input['email'])));
            return Redirect::route('account.login')->withInput()->withErrors($val->errors())
                ->with('error', 'You have been banned. Please contact support.');
        }

        Event::fire('user.loginsuccessful', array(array('Email' => $input['email'])));
        return Redirect::intended(Config::get('graham-campbell/core::home', '/'));
    }

    /**
     * Logout the specified user.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        Event::fire('user.logout', array(array('Email' => $this->credentials->getUser()->email)));
        $this->credentials->logout();
        return Redirect::to(Config::get('graham-campbell/core::home', '/'));
    }

    /**
     * Return the viewer instance.
     *
     * @return \GrahamCampbell\Viewer\Classes\Viewer
     */
    public function getViewer()
    {
        return $this->viewer;
    }

    /**
     * Return the binput instance.
     *
     * @return \GrahamCampbell\Binput\Classes\Binput
     */
    public function getBinput()
    {
        return $this->binput;
    }

    /**
     * Return the user provider instance.
     *
     * @return \GrahamCampbell\Credentials\Providers\UserProvider
     */
    public function getUserProvider()
    {
        return $this->userprovider;
    }
}
