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

use Illuminate\View\Factory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use GrahamCampbell\Binput\Binput;
use GrahamCampbell\Credentials\Credentials;
use GrahamCampbell\Credentials\Providers\UserProvider;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * This is the resend controller class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
class ActivationController extends BaseController
{
    /**
     * Create a new instance.
     *
     * @param  \GrahamCampbell\Credentials\Credentials  $credentials
     * @param  \GrahamCampbell\Binput\Binput  $binput
     * @param  \GrahamCampbell\Credentials\Providers\UserProvider  $userprovider
     * @param  \Illuminate\View\Factory  $view
     * @return void
     */
    public function __construct(Credentials $credentials, Binput $binput, UserProvider $userprovider, Factory $view)
    {
        $this->beforeFilter('throttle.resend', array('only' => array('postResend')));

        parent::__construct($credentials, $binput, $userprovider, $view);
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
            throw new BadRequestHttpException();
        }

        try {
            $user = $this->credentials->getUserProvider()->findById($id);

            if (!$user->attemptActivation($code)) {
                return Redirect::to(Config::get('graham-campbell/core::home', '/'))
                    ->with('error', 'There was a problem activating this account. Please contact support.');
            }

            $user->addGroup($this->credentials->getGroupProvider()->findByName('Users'));

            Event::fire('user.activationsuccessful', array(array('Email' => $user->email)));
            return Redirect::route('account.login')
                ->with('success', 'Your account has been activated successfully. You may now login.');
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Event::fire('user.activationfailed');
            return Redirect::to(Config::get('graham-campbell/core::home', '/'))
                ->with('error', 'There was a problem activating this account. Please contact support.');
        } catch (\Cartalyst\Sentry\Users\UserAlreadyActivatedException $e) {
            Event::fire('user.activationfailed', array(array('Email' => $user->email)));
            return Redirect::route('account.login')
                ->with('warning', 'You have already activated this account. You may want to login.');
        }
    }

    /**
     * Display the resend form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getResend()
    {
        return $this->view->make('graham-campbell/credentials::account.resend');
    }

    /**
     * Queue the sending of the activation email.
     *
     * @return \Illuminate\Http\Response
     */
    public function postResend()
    {
        $input = $this->binput->only('email');

        $val = $this->userprovider->validate($input, array_keys($input));
        if ($val->fails()) {
            return Redirect::route('account.resend')->withInput()->withErrors($val->errors());
        }

        try {
            $user = $this->credentials->getUserProvider()->findByLogin($input['email']);

            if ($user->activated) {
                return Redirect::route('account.resend')->withInput()
                    ->with('error', 'That user is already activated.');
            }

            $mail = array(
                'url'     => URL::to(Config::get('graham-campbell/core::home', '/')),
                'link'    => URL::route('account.activate', array('id' => $user->id, 'code' => $user->getActivationCode())),
                'email'   => $user->getLogin(),
                'subject' => Config::get('platform.name').' - Activation'
            );

            Mail::queue('graham-campbell/credentials::emails.resend', $mail, function($message) use ($mail) {
                $message->to($mail['email'])->subject($mail['subject']);
            });

            return Redirect::route('account.resend')
                ->with('success', 'Check your email for your new activation email.');
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            return Redirect::route('account.resend')
                ->with('error', 'That user does not exist.');
        }
    }
}
