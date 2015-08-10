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

use Cartalyst\Sentry\Users\UserAlreadyActivatedException;
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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * This is the resend controller class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ActivationController extends AbstractController
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

        $this->beforeFilter('throttle.activate', ['only' => ['getActivate']]);
        $this->beforeFilter('throttle.resend', ['only' => ['postResend']]);

        parent::__construct();
    }

    /**
     * Activate an existing user.
     *
     * @param int    $id
     * @param string $code
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     *
     * @return \Illuminate\Http\Response
     */
    public function getActivate($id, $code)
    {
        if (!$id || !$code) {
            throw new BadRequestHttpException();
        }

        try {
            $user = Credentials::getUserProvider()->findById($id);

            if (!$user->attemptActivation($code)) {
                return Redirect::to(Config::get('credentials.home', '/'))
                    ->with('error', 'There was a problem activating this account. Please contact support.');
            }

            $user->addGroup(Credentials::getGroupProvider()->findByName('Users'));

            return Redirect::route('account.login')
                ->with('success', 'Your account has been activated successfully. You may now login.');
        } catch (UserNotFoundException $e) {
            return Redirect::to(Config::get('credentials.home', '/'))
                ->with('error', 'There was a problem activating this account. Please contact support.');
        } catch (UserAlreadyActivatedException $e) {
            return Redirect::route('account.login')
                ->with('warning', 'You have already activated this account. You may want to login.');
        }
    }

    /**
     * Display the resend form.
     *
     * @return \Illuminate\View\View
     */
    public function getResend()
    {
        return View::make('credentials::account.resend');
    }

    /**
     * Queue the sending of the activation email.
     *
     * @return \Illuminate\Http\Response
     */
    public function postResend()
    {
        $input = Binput::only('email');

        $val = UserRepository::validate($input, array_keys($input));
        if ($val->fails()) {
            return Redirect::route('account.resend')->withInput()->withErrors($val->errors());
        }

        $this->throttler->hit();

        try {
            $user = Credentials::getUserProvider()->findByLogin($input['email']);

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

            return Redirect::route('account.resend')
                ->with('success', 'Check your email for your new activation email.');
        } catch (UserNotFoundException $e) {
            return Redirect::route('account.resend')
                ->with('error', 'That user does not exist.');
        }
    }
}
