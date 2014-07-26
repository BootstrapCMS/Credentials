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

use GrahamCampbell\Binput\Facades\Binput;
use GrahamCampbell\Credentials\Facades\Credentials;
use GrahamCampbell\Credentials\Facades\UserProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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
     * @return void
     */
    public function __construct()
    {
        $this->setPermissions(array(
            'getHistory'    => 'user',
            'getProfile'    => 'user',
            'deleteProfile' => 'user',
            'patchDetails'  => 'user',
            'patchPassword' => 'user',
        ));

        parent::__construct();
    }

    /**
     * Display the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function getHistory()
    {
        return View::make(
            'graham-campbell/credentials::account.history',
            array('user' => Credentials::getUser())
        );
    }

    /**
     * Display the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function getProfile()
    {
        return View::make(
            'graham-campbell/credentials::account.profile',
            array('user' => Credentials::getUser())
        );
    }

    /**
     * Delete the user's profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteProfile()
    {
        $user = Credentials::getUser();
        $this->checkUser($user);

        Credentials::logout();

        try {
            $user->delete();
        } catch (\Exception $e) {
            return Redirect::to(Config::get('graham-campbell/core::home', '/'))
                ->with('error', 'There was a problem deleting your account.');
        }

        return Redirect::to(Config::get('graham-campbell/core::home', '/'))
            ->with('success', 'Your account has been deleted successfully.');
    }

    /**
     * Update the user's details.
     *
     * @return \Illuminate\Http\Response
     */
    public function patchDetails()
    {
        $input = Binput::only(array('first_name', 'last_name', 'email'));

        $val = UserProvider::validate($input, array_keys($input));
        if ($val->fails()) {
            return Redirect::route('account.profile')->withInput()->withErrors($val->errors());
        }

        $user = Credentials::getUser();
        $this->checkUser($user);

        $user->update($input);

        return Redirect::route('account.profile')
            ->with('success', 'Your details have been updated successfully.');
    }

    /**
     * Update the user's password.
     *
     * @return \Illuminate\Http\Response
     */
    public function patchPassword()
    {
        $input = Binput::only(array('password', 'password_confirmation'));

        $val = UserProvider::validate($input, array_keys($input));
        if ($val->fails()) {
            return Redirect::route('account.profile')->withInput()->withErrors($val->errors());
        }

        unset($input['password_confirmation']);

        $user = Credentials::getUser();
        $this->checkUser($user);

        $mail = array(
            'url'     => URL::to(Config::get('graham-campbell/core::home', '/')),
            'email'   => $user->getLogin(),
            'subject' => Config::get('platform.name').' - New Password Notification'
        );

        Mail::queue('graham-campbell/credentials::emails.newpass', $mail, function($message) use ($mail) {
            $message->to($mail['email'])->subject($mail['subject']);
        });

        $user->update($input);

        return Redirect::route('account.profile')
            ->with('success', 'Your password has been updated successfully.');
    }

    /**
     * Check the user model.
     *
     * @param  mixed  $user
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function checkUser($user)
    {
        if (!$user) {
            throw new NotFoundHttpException('User Not Found');
        }
    }
}
