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

use GrahamCampbell\Binput\Facades\Binput;
use GrahamCampbell\Credentials\Facades\Credentials;
use GrahamCampbell\Credentials\Facades\UserRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This is the account controller class.
 *
 * @author Graham Campbell <graham@alt-three.com>
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
        $this->setPermissions([
            'getHistory'    => 'user',
            'getProfile'    => 'user',
            'deleteProfile' => 'user',
            'patchDetails'  => 'user',
            'patchPassword' => 'user',
        ]);

        parent::__construct();
    }

    /**
     * Display the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function getHistory()
    {
        return View::make('credentials::account.history')->withUser(Credentials::getUser());
    }

    /**
     * Display the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function getProfile()
    {
        return View::make('credentials::account.profile')->withUser(Credentials::getUser());
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

        $email = $user->getLogin();

        Credentials::logout();

        try {
            $user->delete();
        } catch (\Exception $e) {
            return Redirect::to(Config::get('credentials.home', '/'))
                ->with('error', 'There was a problem deleting your account.');
        }

        $mail = [
            'url'     => URL::to(Config::get('credentials.home', '/')),
            'email'   => $email,
            'subject' => Config::get('app.name').' - Account Deleted Notification',
        ];

        Mail::queue('credentials::emails.userdeleted', $mail, function ($message) use ($mail) {
            $message->to($mail['email'])->subject($mail['subject']);
        });

        return Redirect::to(Config::get('credentials.home', '/'))
            ->with('success', 'Your account has been deleted successfully.');
    }

    /**
     * Update the user's details.
     *
     * @return \Illuminate\Http\Response
     */
    public function patchDetails()
    {
        $input = Binput::only(['first_name', 'last_name', 'email']);

        $val = UserRepository::validate($input, array_keys($input));
        if ($val->fails()) {
            return Redirect::route('account.profile')->withInput()->withErrors($val->errors());
        }

        $user = Credentials::getUser();
        $this->checkUser($user);

        $email = $user['email'];

        $user->update($input);

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
        $input = Binput::only(['password', 'password_confirmation']);

        $val = UserRepository::validate($input, array_keys($input));
        if ($val->fails()) {
            return Redirect::route('account.profile')->withInput()->withErrors($val->errors());
        }

        unset($input['password_confirmation']);

        $user = Credentials::getUser();
        $this->checkUser($user);

        $mail = [
            'url'     => URL::to(Config::get('credentials.home', '/')),
            'email'   => $user->getLogin(),
            'subject' => Config::get('app.name').' - New Password Notification',
        ];

        Mail::queue('credentials::emails.newpass', $mail, function ($message) use ($mail) {
            $message->to($mail['email'])->subject($mail['subject']);
        });

        $user->update($input);

        return Redirect::route('account.profile')
            ->with('success', 'Your password has been updated successfully.');
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
