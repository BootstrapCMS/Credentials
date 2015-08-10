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
use Cartalyst\Sentry\Users\UserNotActivatedException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Cartalyst\Sentry\Users\WrongPasswordException;
use GrahamCampbell\Binput\Facades\Binput;
use GrahamCampbell\Credentials\Facades\Credentials;
use GrahamCampbell\Credentials\Facades\UserRepository;
use GrahamCampbell\Credentials\Http\Middleware\SentryThrottle;
use GrahamCampbell\Throttle\Throttlers\ThrottlerInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

/**
 * This is the login controller class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class LoginController extends AbstractController
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

        $this->setPermissions([
            'getLogout' => 'user',
        ]);

        $this->beforeFilter('throttle.login', ['only' => ['postLogin']]);
        $this->middleware(SentryThrottle::class, ['only' => ['postLogin']]);

        parent::__construct();
    }

    /**
     * Display the login form.
     *
     * @return \Illuminate\View\View
     */
    public function getLogin()
    {
        return View::make('credentials::account.login');
    }

    /**
     * Attempt to login the specified user.
     *
     * @return \Illuminate\Http\Response
     */
    public function postLogin()
    {
        $remember = Binput::get('rememberMe');

        $input = Binput::only(['email', 'password']);

        $rules = UserRepository::rules(array_keys($input));
        $rules['password'] = 'required|min:6';

        $val = UserRepository::validate($input, $rules, true);
        if ($val->fails()) {
            return Redirect::route('account.login')->withInput()->withErrors($val->errors());
        }

        $this->throttler->hit();

        try {
            $throttle = Credentials::getThrottleProvider()->findByUserLogin($input['email']);
            $throttle->check();

            Credentials::authenticate($input, $remember);
        } catch (WrongPasswordException $e) {
            return Redirect::route('account.login')->withInput()->withErrors($val->errors())
                ->with('error', 'Your password was incorrect.');
        } catch (UserNotFoundException $e) {
            return Redirect::route('account.login')->withInput()->withErrors($val->errors())
                ->with('error', 'That user does not exist.');
        } catch (UserNotActivatedException $e) {
            if (Config::get('credentials::activation')) {
                return Redirect::route('account.login')->withInput()->withErrors($val->errors())
                ->with('error', 'You have not yet activated this account.');
            } else {
                $throttle->user->attemptActivation($throttle->user->getActivationCode());
                $throttle->user->addGroup(Credentials::getGroupProvider()->findByName('Users'));

                return $this->postLogin();
            }
        } catch (UserSuspendedException $e) {
            $time = $throttle->getSuspensionTime();

            return Redirect::route('account.login')->withInput()->withErrors($val->errors())
                ->with('error', "Your account has been suspended for $time minutes.");
        } catch (UserBannedException $e) {
            return Redirect::route('account.login')->withInput()->withErrors($val->errors())
                ->with('error', 'You have been banned. Please contact support.');
        }

        return Redirect::intended(Config::get('credentials.home', '/'));
    }

    /**
     * Logout the specified user.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        Credentials::logout();

        return Redirect::to(Config::get('credentials.home', '/'));
    }
}
