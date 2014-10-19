<?php

/**
 * This file is part of Laravel Credentials by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at http://bit.ly/UWsjkb.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace GrahamCampbell\Credentials\Controllers;

use Cartalyst\Sentry\Users\UserExistsException;
use GrahamCampbell\Binput\Facades\Binput;
use GrahamCampbell\Credentials\Facades\Credentials;
use GrahamCampbell\Credentials\Facades\UserProvider;
use GrahamCampbell\Throttle\Throttlers\ThrottlerInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

/**
 * This is the registration controller class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md> Apache 2.0
 */
class RegistrationController extends AbstractController
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
     * @param \GrahamCampbell\Throttle\Throttlers\ThrottlerInterface $throttler
     *
     * @return void
     */
    public function __construct(ThrottlerInterface $throttler)
    {
        $this->throttler = $throttler;

        $this->beforeFilter('throttle.register', array('only' => array('postRegister')));

        parent::__construct();
    }

    /**
     * Display the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function getRegister()
    {
        return View::make('graham-campbell/credentials::account.register');
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

        $input = Binput::only(array('first_name', 'last_name', 'email', 'password', 'password_confirmation'));

        $val = UserProvider::validate($input, array_keys($input));
        if ($val->fails()) {
            return Redirect::route('account.register')->withInput()->withErrors($val->errors());
        }

        $this->throttler->hit();

        try {
            unset($input['password_confirmation']);

            $user = Credentials::register($input);

            if (!Config::get('graham-campbell/credentials::activation')) {
                $mail = array(
                    'url'     => URL::to(Config::get('graham-campbell/core::home', '/')),
                    'email'   => $user->getLogin(),
                    'subject' => Config::get('platform.name').' - Welcome',
                );

                Mail::queue('graham-campbell/credentials::emails.welcome', $mail, function ($message) use ($mail) {
                    $message->to($mail['email'])->subject($mail['subject']);
                });

                $user->attemptActivation($user->getActivationCode());
                $user->addGroup(Credentials::getGroupProvider()->findByName('Users'));

                return Redirect::to(Config::get('graham-campbell/core::home', '/'))
                    ->with('success', 'Your account has been created successfully.');
            }

            $code = $user->getActivationCode();

            $mail = array(
                'url'     => URL::to(Config::get('graham-campbell/core::home', '/')),
                'link'    => URL::route('account.activate', array('id' => $user->id, 'code' => $code)),
                'email'   => $user->getLogin(),
                'subject' => Config::get('platform.name').' - Welcome',
            );

            Mail::queue('graham-campbell/credentials::emails.welcome', $mail, function ($message) use ($mail) {
                $message->to($mail['email'])->subject($mail['subject']);
            });

            return Redirect::to(Config::get('graham-campbell/core::home', '/'))
                ->with('success', 'Your account has been created. Check your email for the confirmation link.');
        } catch (UserExistsException $e) {
            return Redirect::route('account.register')->withInput()->withErrors($val->errors())
                ->with('error', 'That email address is taken.');
        }
    }
}
