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

use GrahamCampbell\Binput\Binput;
use GrahamCampbell\Credentials\Credentials;
use GrahamCampbell\Credentials\Providers\UserProvider;
use GrahamCampbell\Throttle\Throttlers\ThrottlerInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\Factory;

/**
 * This is the login controller class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
class LoginController extends BaseController
{
    /**
     * The throttler instance.
     *
     * @var \GrahamCampbell\Throttle\Throttlers\ThrottlerInterface
     */
    protected $throttler;

    /**
     * Create a new instance.
     *
     * @param  \GrahamCampbell\Credentials\Credentials  $credentials
     * @param  \GrahamCampbell\Binput\Binput  $binput
     * @param  \GrahamCampbell\Credentials\Providers\UserProvider  $userprovider
     * @param  \Illuminate\View\Factory  $view
     * @param  \GrahamCampbell\Throttle\Throttlers\ThrottlerInterface  $throttler
     * @return void
     */
    public function __construct(
        Credentials $credentials,
        Binput $binput,
        UserProvider $userprovider,
        Factory $view,
        ThrottlerInterface $throttler
    ) {
        $this->setPermissions(array(
            'getLogout' => 'user',
        ));

        $this->beforeFilter('throttle.login', array('only' => array('postLogin')));
        $this->beforeFilter('throttle.sentry', array('only' => array('postLogin')));

        $this->throttler = $throttler;

        parent::__construct($credentials, $binput, $userprovider, $view);
    }

    /**
     * Display the login form.
     *
     * @return \Illuminate\View\View
     */
    public function getLogin()
    {
        return $this->view->make('graham-campbell/credentials::account.login');
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

        $messages = $val->messages()->all();

        $val = $this->userprovider->validate($input, $rules, true);
        if ($val->fails()) {
            return Redirect::route('account.login')->withInput()->withErrors($val->errors());
        }

        $this->throttler->hit();

        try {
            $throttle = $this->credentials->getThrottleProvider()->findByUserLogin($input['email']);
            $throttle->check();

            $this->credentials->authenticate($input, $remember);
        } catch (\Cartalyst\Sentry\Users\WrongPasswordException $e) {
            return Redirect::route('account.login')->withInput()->withErrors($val->errors())
                ->with('error', 'Your password was incorrect.');
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            return Redirect::route('account.login')->withInput()->withErrors($val->errors())
                ->with('error', 'That user does not exist.');
        } catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e) {
            if (Config::get('graham-campbell/credentials::activation')) {
                return Redirect::route('account.login')->withInput()->withErrors($val->errors())
                ->with('error', 'You have not yet activated this account.');
            } else {
                $throttle->user->attemptActivation($throttle->user->getActivationCode());
                $throttle->user->addGroup($this->credentials->getGroupProvider()->findByName('Users'));
                return $this->postLogin();
            }
        } catch (\Cartalyst\Sentry\Throttling\UserSuspendedException $e) {
            $time = $throttle->getSuspensionTime();
            return Redirect::route('account.login')->withInput()->withErrors($val->errors())
                ->with('error', "Your account has been suspended for $time minutes.");
        } catch (\Cartalyst\Sentry\Throttling\UserBannedException $e) {
            return Redirect::route('account.login')->withInput()->withErrors($val->errors())
                ->with('error', 'You have been banned. Please contact support.');
        }

        return Redirect::intended(Config::get('graham-campbell/core::home', '/'));
    }

    /**
     * Logout the specified user.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        $this->credentials->logout();

        return Redirect::to(Config::get('graham-campbell/core::home', '/'));
    }
}
