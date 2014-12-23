<?php

/*
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

use Cartalyst\Sentry\Throttling\UserBannedException;
use Cartalyst\Sentry\Throttling\UserSuspendedException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use DateTime;
use GrahamCampbell\Binput\Facades\Binput;
use GrahamCampbell\Credentials\Facades\Credentials;
use GrahamCampbell\Credentials\Facades\GroupProvider;
use GrahamCampbell\Credentials\Facades\UserProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This is the user controller class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md> Apache 2.0
 */
class UserController extends AbstractController
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setPermissions(array(
            'index'   => 'mod',
            'create'  => 'admin',
            'store'   => 'admin',
            'show'    => 'mod',
            'edit'    => 'admin',
            'update'  => 'admin',
            'suspend' => 'mod',
            'reset'   => 'admin',
            'resend'  => 'admin',
            'destroy' => 'admin',
        ));

        parent::__construct();
    }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = UserProvider::paginate();
        $links = UserProvider::links();

        return View::make(
            'graham-campbell/credentials::users.index',
            array('users' => $users, 'links' => $links)
        );
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $groups = GroupProvider::index();

        return View::make(
            'graham-campbell/credentials::users.create',
            array('groups' => $groups)
        );
    }

    /**
     * Store a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $password = Str::random();

        $input = array_merge(Binput::only(array('first_name', 'last_name', 'email')), array(
            'password'     => $password,
            'activated'    => true,
            'activated_at' => new DateTime(),
        ));

        $rules = UserProvider::rules(array_keys($input));
        $rules['password'] = 'required|min:6';

        $val = UserProvider::validate($input, $rules, true);
        if ($val->fails()) {
            return Redirect::route('users.create')->withInput()->withErrors($val->errors());
        }

        try {
            $user = UserProvider::create($input);

            $groups = GroupProvider::index();
            foreach ($groups as $group) {
                if (Binput::get('group_'.$group->id) === 'on') {
                    $user->addGroup($group);
                }
            }

            $mail = array(
                'url'      => URL::to(Config::get('graham-campbell/core::home', '/')),
                'password' => $password,
                'email'    => $user->getLogin(),
                'subject'  => Config::get('platform.name').' - New Account Information',
            );

            Mail::queue('graham-campbell/credentials::emails.newuser', $mail, function ($message) use ($mail) {
                $message->to($mail['email'])->subject($mail['subject']);
            });

            return Redirect::route('users.show', array('users' => $user->id))
                ->with('success', 'The user has been created successfully. Their password has been emailed to them.');
        } catch (UserExistsException $e) {
            return Redirect::route('users.create')->withInput()->withErrors($val->errors())
                ->with('error', 'That email address is taken.');
        }
    }

    /**
     * Show the specified user.
     *
     * @param int $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = UserProvider::find($id);
        $this->checkUser($user);

        if ($user->activated_at) {
            $activated = HTML::ago($user->activated_at);
        } else {
            if (Credentials::hasAccess('admin') && Config::get('graham-campbell/credentials::activation')) {
                $activated = 'No - <a href="#resend_user" data-toggle="modal" data-target="#resend_user">Resend Email</a>';
            } else {
                $activated = 'Not Activated';
            }
        }

        if (Credentials::getThrottleProvider()->findByUserId($id)->isSuspended()) {
            $suspended = 'Currently Suspended';
        } else {
            $suspended = 'Not Suspended';
        }

        $groups = $user->getGroups();
        if (count($groups) >= 1) {
            $data = array();
            foreach ($groups as $group) {
                $data[] = $group->name;
            }
            $groups = implode(', ', $data);
        } else {
            $groups = 'No Group Memberships';
        }

        return View::make(
            'graham-campbell/credentials::users.show',
            array('user' => $user, 'groups' => $groups, 'activated' => $activated, 'suspended' => $suspended)
        );
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param int $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = UserProvider::find($id);
        $this->checkUser($user);

        $groups = GroupProvider::index();

        return View::make(
            'graham-campbell/credentials::users.edit',
            array('user' => $user, 'groups' => $groups)
        );
    }

    /**
     * Update an existing user.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $input = Binput::only(array('first_name', 'last_name', 'email'));

        $val = UserProvider::validate($input, array_keys($input));
        if ($val->fails()) {
            return Redirect::route('users.edit', array('users' => $id))
                ->withInput()->withErrors($val->errors());
        }

        $user = UserProvider::find($id);
        $this->checkUser($user);

        $email = $user['email'];

        $user->update($input);

        $groups = GroupProvider::index();

        $changed = false;

        foreach ($groups as $group) {
            if ($user->inGroup($group)) {
                if (Binput::get('group_'.$group->id) !== 'on') {
                    $user->removeGroup($group);
                    $changed = true;
                }
            } else {
                if (Binput::get('group_'.$group->id) === 'on') {
                    $user->addGroup($group);
                    $changed = true;
                }
            }
        }

        if ($email !== $input['email']) {
            $mail = array(
                'old'     => $email,
                'new'     => $input['email'],
                'url'     => URL::to(Config::get('graham-campbell/core::home', '/')),
                'subject' => Config::get('platform.name').' - New Email Information',
            );

            Mail::queue('graham-campbell/credentials::emails.newemail', $mail, function ($message) use ($mail) {
                $message->to($mail['old'])->subject($mail['subject']);
            });

            Mail::queue('graham-campbell/credentials::emails.newemail', $mail, function ($message) use ($mail) {
                $message->to($mail['new'])->subject($mail['subject']);
            });
        }

        if ($changed) {
            $mail = array(
                'url'     => URL::to(Config::get('graham-campbell/core::home', '/')),
                'email'   => $input['email'],
                'subject' => Config::get('platform.name').' - Group Membership Changes',
            );

            Mail::queue('graham-campbell/credentials::emails.groups', $mail, function ($message) use ($mail) {
                $message->to($mail['email'])->subject($mail['subject']);
            });
        }

        return Redirect::route('users.show', array('users' => $user->id))
            ->with('success', 'The user has been updated successfully.');
    }

    /**
     * Suspend an existing user.
     *
     * @param int $id
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return \Illuminate\Http\Response
     */
    public function suspend($id)
    {
        try {
            $throttle = Credentials::getThrottleProvider()->findByUserId($id);
            $throttle->suspend();
        } catch (UserNotFoundException $e) {
            throw new NotFoundHttpException('User Not Found', $e);
        } catch (UserSuspendedException $e) {
            $time = $throttle->getSuspensionTime();
            return Redirect::route('users.suspend', array('users' => $id))->withInput()
                ->with('error', "This user is already suspended for $time minutes.");
        } catch (UserBannedException $e) {
            return Redirect::route('users.suspend', array('users' => $id))->withInput()
                ->with('error', 'This user has already been banned.');
        }

        return Redirect::route('users.show', array('users' => $id))
            ->with('success', 'The user has been suspended successfully.');
    }

    /**
     * Reset the password of an existing user.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function reset($id)
    {
        $password = Str::random();

        $input = array(
            'password' => $password,
        );

        $rules = array(
            'password' => 'required|min:6',
        );

        $val = UserProvider::validate($input, $rules, true);
        if ($val->fails()) {
            return Redirect::route('users.show', array('users' => $id))->withErrors($val->errors());
        }

        $user = UserProvider::find($id);
        $this->checkUser($user);

        $user->update($input);

        $mail = array(
            'password' => $password,
            'email' => $user->getLogin(),
            'subject' => Config::get('platform.name').' - New Password Information',
        );

        Mail::queue('graham-campbell/credentials::emails.password', $mail, function ($message) use ($mail) {
            $message->to($mail['email'])->subject($mail['subject']);
        });

        return Redirect::route('users.show', array('users' => $id))
            ->with('success', 'The user\'s password has been reset successfully, and has been emailed to them.');
    }

    /**
     * Resend the activation email of an existing user.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function resend($id)
    {
        $user = UserProvider::find($id);
        $this->checkUser($user);

        if ($user->activated) {
            return Redirect::route('account.resend')->withInput()
                ->with('error', 'That user is already activated.');
        }

        $code = $user->getActivationCode();

        $mail = array(
            'url'     => URL::to(Config::get('graham-campbell/core::home', '/')),
            'link'    => URL::route('account.activate', array('id' => $user->id, 'code' => $code)),
            'email'   => $user->getLogin(),
            'subject' => Config::get('platform.name').' - Activation',
        );

        Mail::queue('graham-campbell/credentials::emails.resend', $mail, function ($message) use ($mail) {
            $message->to($mail['email'])->subject($mail['subject']);
        });

        return Redirect::route('users.show', array('users' => $id))
            ->with('success', 'The user\'s activation email has been sent successfully.');
    }

    /**
     * Delete an existing user.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = UserProvider::find($id);
        $this->checkUser($user);

        $email = $user->getLogin();

        try {
            $user->delete();
        } catch (\Exception $e) {
            return Redirect::route('users.show', array('users' => $id))
                ->with('error', 'We were unable to delete the account.');
        }

        $mail = array(
            'url'     => URL::to(Config::get('graham-campbell/core::home', '/')),
            'email'   => $email,
            'subject' => Config::get('platform.name').' - Account Deleted Notification',
        );

        Mail::queue('graham-campbell/credentials::emails.admindeleted', $mail, function ($message) use ($mail) {
            $message->to($mail['email'])->subject($mail['subject']);
        });

        return Redirect::route('users.index')
            ->with('success', 'The user has been deleted successfully.');
    }

    /**
     * Check the user model.
     *
     * @param mixed $user
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return void
     */
    protected function checkUser($user)
    {
        if (!$user) {
            throw new NotFoundHttpException('User Not Found');
        }
    }
}
