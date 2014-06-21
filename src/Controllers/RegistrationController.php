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
use Illuminate\Support\Facades\URL;
use GrahamCampbell\Binput\Classes\Binput;
use GrahamCampbell\Viewer\Classes\Viewer;
use GrahamCampbell\Queuing\Facades\Queuing;
use GrahamCampbell\Credentials\Credentials;
use GrahamCampbell\Credentials\Providers\UserProvider;

/**
 * This is the registration controller class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
class RegistrationController extends AbstractController
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
     * @param  \GrahamCampbell\Credentials\Credentials  $credentials
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

        $this->beforeFilter('throttle.register', array('only' => array('postRegister')));

        parent::__construct($credentials);
    }

    /**
     * Display the registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        return $this->viewer->make('graham-campbell/credentials::account.register');
    }

    /**
     * Attempt to register a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function postRegister()
    {
        if (!Config::get('graham-campbell/credentials::regallowed')) {
            return Redirect::route('account.register');
        }

        $input = $this->binput->only(array('first_name', 'last_name', 'email', 'password', 'password_confirmation'));

        $val = $this->userprovider->validate($input, array_keys($input));
        if ($val->fails()) {
            Event::fire('user.registrationfailed', array(array('Email' => $input['email'], 'Messages' => $val->messages()->all())));
            return Redirect::route('account.register')->withInput()->withErrors($val->errors());
        }

        try {
            unset($input['password_confirmation']);

            $user = $this->credentials->register($input);

            if (!Config::get('graham-campbell/credentials::activation')) {
                try {
                    $data = array(
                        'view'    => 'graham-campbell/credentials::emails.welcome',
                        'url'     => URL::to(Config::get('graham-campbell/core::home', '/')),
                        'email'   => $user->getLogin(),
                        'subject' => Config::get('platform.name').' - Welcome'
                    );

                    Queuing::pushMail($data);
                } catch (\Exception $e) {
                    Event::fire('user.registrationfailed', array(array('Email' => $input['email'])));
                    $user->delete();
                    return Redirect::route('account.register')->withInput()
                        ->with('error', 'We were unable to create your account. Please contact support.');
                }

                $user->attemptActivation($user->getActivationCode());
                $user->addGroup($this->credentials->getGroupProvider()->findByName('Users'));

                Event::fire('user.registrationsuccessful', array(array('Email' => $input['email'], 'Activated' => true)));
                return Redirect::to(Config::get('graham-campbell/core::home', '/'))
                    ->with('success', 'Your account has been created successfully.');
            }

            try {
                $data = array(
                    'view'    => 'graham-campbell/credentials::emails.welcome',
                    'url'     => URL::to(Config::get('graham-campbell/core::home', '/')),
                    'link'    => URL::route('account.activate', array('id' => $user->id, 'code' => $user->getActivationCode())),
                    'email'   => $user->getLogin(),
                    'subject' => Config::get('platform.name').' - Welcome'
                );

                Queuing::pushMail($data);
            } catch (\Exception $e) {
                Event::fire('user.registrationfailed', array(array('Email' => $input['email'])));
                $user->delete();
                return Redirect::route('account.register')->withInput()
                    ->with('error', 'We were unable to create your account. Please contact support.');
            }

            Event::fire('user.registrationsuccessful', array(array('Email' => $input['email'], 'Activated' => false)));
            return Redirect::to(Config::get('graham-campbell/core::home', '/'))
                ->with('success', 'Your account has been created. Check your email for the confirmation link.');
        } catch (\Cartalyst\Sentry\Users\UserExistsException $e) {
            Event::fire('user.registrationfailed', array(array('Email' => $input['email'])));
            return Redirect::route('account.register')->withInput()->withErrors($val->errors())
                ->with('error', 'That email address is taken.');
        }
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
