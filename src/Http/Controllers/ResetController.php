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

use Cartalyst\Sentry\Users\UserNotFoundException;
use GrahamCampbell\Binput\Facades\Binput;
use GrahamCampbell\Credentials\Facades\Credentials;
use GrahamCampbell\Credentials\Facades\UserRepository;
use GrahamCampbell\Throttle\Throttlers\ThrottlerInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * This is the reset controller class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ResetController extends AbstractController
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

        $this->beforeFilter('throttle.reset', ['only' => ['postReset']]);

        parent::__construct();
    }

    /**
     * Display the password reset form.
     *
     * @return \Illuminate\View\View
     */
    public function getReset()
    {
        return View::make('credentials::account.reset');
    }

    /**
     * Queue the sending of the password reset email.
     *
     * @return \Illuminate\Http\Response
     */
    public function postReset()
    {
        $input = Binput::only('email');

        $val = UserRepository::validate($input, array_keys($input));
        if ($val->fails()) {
            return Redirect::route('account.reset')->withInput()->withErrors($val->errors());
        }

        $this->throttler->hit();

        try {
            $user = Credentials::getUserProvider()->findByLogin($input['email']);

            $code = $user->getResetPasswordCode();

            $mail = [
                'link'    => URL::route('account.password', ['id'    => $user->id, 'code'    => $code]),
                'email'   => $user->getLogin(),
                'subject' => Config::get('app.name').' - '.trans('credentials.password_reset_confirmation'),
            ];

            Mail::queue('credentials::emails.reset', $mail, function ($message) use ($mail) {
                $message->to($mail['email'])->subject($mail['subject']);
            });

            return Redirect::route('account.reset')
                ->with('success', trans('credentials.check_your_email_for_password_reset'));
        } catch (UserNotFoundException $e) {
            return Redirect::route('account.reset')
                ->with('error', trans('credentials.that_user_does_not_exist'));
        }
    }

    /**
     * Reset the user's password.
     *
     * @param int    $id
     * @param string $code
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     *
     * @return \Illuminate\Http\Response
     */
    public function getPassword($id, $code)
    {
        if (!$id || !$code) {
            throw new BadRequestHttpException();
        }

        try {
            $user = Credentials::getUserProvider()->findById($id);

            $password = Str::random();

            if (!$user->attemptResetPassword($code, $password)) {
                return Redirect::to(Config::get('credentials.home', '/'))
                    ->with('error', trans('credentials.there_was_a_problem_resetting_your_password'));
            }

            $mail = [
                'password' => $password,
                'email'    => $user->getLogin(),
                'subject'  => Config::get('app.name').' - '.trans('credentials.new_password_information'),
            ];

            Mail::queue('credentials::emails.password', $mail, function ($message) use ($mail) {
                $message->to($mail['email'])->subject($mail['subject']);
            });

            return Redirect::to(Config::get('credentials.home', '/'))
                ->with('success', trans('credentials.your_password_has_been_changed'));
        } catch (UserNotFoundException $e) {
            return Redirect::to(Config::get('credentials.home', '/'))
                ->with('error', trans('credentials.there_was_a_problem_resetting_your_password'));
        }
    }
}
