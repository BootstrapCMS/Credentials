<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Http\Controllers;

use Cartalyst\Sentry\Throttling\UserBannedException;
use Cartalyst\Sentry\Throttling\UserSuspendedException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use DateTime;
use GrahamCampbell\Binput\Facades\Binput;
use GrahamCampbell\Credentials\Facades\Credentials;
use GrahamCampbell\Credentials\Facades\GroupRepository;
use GrahamCampbell\Credentials\Facades\UserRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This is the user controller class.
 *
 * @author Graham Campbell <graham@alt-three.com>
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
        $this->setPermissions([
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
        ]);

        parent::__construct();
    }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = UserRepository::paginate();
        $links = UserRepository::links();

        return View::make('credentials::users.index', compact('users', 'links'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $groups = GroupRepository::index();

        return View::make('credentials::users.create', compact('groups'));
    }

    /**
     * Store a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $password = Str::random();

        $input = array_merge(Binput::only(['first_name', 'last_name', 'email']), [
            'password'     => $password,
            'activated'    => true,
            'activated_at' => new DateTime(),
        ]);

        $rules = UserRepository::rules(array_keys($input));
        $rules['password'] = 'required|min:6';

        $val = UserRepository::validate($input, $rules, true);
        if ($val->fails()) {
            return Redirect::route('users.create')->withInput()->withErrors($val->errors());
        }

        try {
            $user = UserRepository::create($input);

            $groups = GroupRepository::index();
            foreach ($groups as $group) {
                if (Binput::get('group_'.$group->id) === 'on') {
                    $user->addGroup($group);
                }
            }

            $mail = [
                'url'      => URL::to(Config::get('credentials.home', '/')),
                'password' => $password,
                'email'    => $user->getLogin(),
                'subject'  => Config::get('app.name').' - New Account Information',
            ];

            Mail::queue('credentials::emails.newuser', $mail, function ($message) use ($mail) {
                $message->to($mail['email'])->subject($mail['subject']);
            });

            return Redirect::route('users.show', ['users' => $user->id])
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
        $user = UserRepository::find($id);
        $this->checkUser($user);

        if ($user->activated_at) {
            $activated = html_ago($user->activated_at);
        } else {
            if (Credentials::hasAccess('admin') && Config::get('credentials.activation')) {
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
            $data = [];
            foreach ($groups as $group) {
                $data[] = $group->name;
            }
            $groups = implode(', ', $data);
        } else {
            $groups = 'No Group Memberships';
        }

        return View::make('credentials::users.show', compact('user', 'groups', 'activated', 'suspended'));
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
        $user = UserRepository::find($id);
        $this->checkUser($user);

        $groups = GroupRepository::index();

        return View::make('credentials::users.edit', compact('user', 'groups'));
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
        $input = Binput::only(['first_name', 'last_name', 'email']);

        $val = UserRepository::validate($input, array_keys($input));
        if ($val->fails()) {
            return Redirect::route('users.edit', ['users' => $id])
                ->withInput()->withErrors($val->errors());
        }

        $user = UserRepository::find($id);
        $this->checkUser($user);

        $email = $user['email'];

        $user->update($input);

        $groups = GroupRepository::index();

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
            $mail = [
                'old'     => $email,
                'new'     => $input['email'],
                'url'     => URL::to(Config::get('credentials.home', '/')),
                'subject' => Config::get('app.name').' - New Email Information',
            ];

            Mail::queue('credentials::emails.newemail', $mail, function ($message) use ($mail) {
                $message->to($mail['old'])->subject($mail['subject']);
            });

            Mail::queue('credentials::emails.newemail', $mail, function ($message) use ($mail) {
                $message->to($mail['new'])->subject($mail['subject']);
            });
        }

        if ($changed) {
            $mail = [
                'url'     => URL::to(Config::get('credentials.home', '/')),
                'email'   => $input['email'],
                'subject' => Config::get('app.name').' - Group Membership Changes',
            ];

            Mail::queue('credentials::emails.groups', $mail, function ($message) use ($mail) {
                $message->to($mail['email'])->subject($mail['subject']);
            });
        }

        return Redirect::route('users.show', ['users' => $user->id])
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

            return Redirect::route('users.suspend', ['users' => $id])->withInput()
                ->with('error', "This user is already suspended for $time minutes.");
        } catch (UserBannedException $e) {
            return Redirect::route('users.suspend', ['users' => $id])->withInput()
                ->with('error', 'This user has already been banned.');
        }

        return Redirect::route('users.show', ['users' => $id])
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

        $input = [
            'password' => $password,
        ];

        $rules = [
            'password' => 'required|min:6',
        ];

        $val = UserRepository::validate($input, $rules, true);
        if ($val->fails()) {
            return Redirect::route('users.show', ['users' => $id])->withErrors($val->errors());
        }

        $user = UserRepository::find($id);
        $this->checkUser($user);

        $user->update($input);

        $mail = [
            'password' => $password,
            'email'    => $user->getLogin(),
            'subject'  => Config::get('app.name').' - New Password Information',
        ];

        Mail::queue('credentials::emails.password', $mail, function ($message) use ($mail) {
            $message->to($mail['email'])->subject($mail['subject']);
        });

        return Redirect::route('users.show', ['users' => $id])
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
        $user = UserRepository::find($id);
        $this->checkUser($user);

        if ($user->activated) {
            return Redirect::route('account.resend')->withInput()
                ->with('error', 'That user is already activated.');
        }

        $code = $user->getActivationCode();

        $mail = [
            'url'     => URL::to(Config::get('credentials.home', '/')),
            'link'    => URL::route('account.activate', ['id' => $user->id, 'code' => $code]),
            'email'   => $user->getLogin(),
            'subject' => Config::get('app.name').' - Activation',
        ];

        Mail::queue('credentials::emails.resend', $mail, function ($message) use ($mail) {
            $message->to($mail['email'])->subject($mail['subject']);
        });

        return Redirect::route('users.show', ['users' => $id])
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
        $user = UserRepository::find($id);
        $this->checkUser($user);

        $email = $user->getLogin();

        try {
            $user->delete();
        } catch (\Exception $e) {
            return Redirect::route('users.show', ['users' => $id])
                ->with('error', 'We were unable to delete the account.');
        }

        $mail = [
            'url'     => URL::to(Config::get('credentials.home', '/')),
            'email'   => $email,
            'subject' => Config::get('app.name').' - Account Deleted Notification',
        ];

        Mail::queue('credentials::emails.admindeleted', $mail, function ($message) use ($mail) {
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
