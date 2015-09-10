<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use GrahamCampbell\Throttle\Facades\Throttle;
use Illuminate\Support\Facades\Redirect;

$router->filter('throttle.login', function ($route, $request) {

    // check if we've reached the rate limit, but don't hit the throttle yet
    // we can hit the throttle later on in the if validation passes
    if (!Throttle::check($request, 10, 10)) {
        return Redirect::route('account.login')->withInput()
            ->with('error', trans('credentials.you_have_made_too_many_login_requests'));
    }
});

$router->filter('throttle.activate', function ($route, $request) {
    // check if we've reached the rate limit, and hit the throttle
    // no validation is required, we should always hit the throttle
    if (!Throttle::attempt($request, 10, 10)) {
        return Redirect::route('account.login')->withInput()
            ->with('error', trans('credentials.you_have_made_too_many_activation_requests'));
    }
});

$router->filter('throttle.resend', function ($route, $request) {
    // check if we've reached the rate limit, but don't hit the throttle yet
    // we can hit the throttle later on in the if validation passes
    if (!Throttle::check($request, 5, 30)) {
        return Redirect::route('account.resend')->withInput()
            ->with('error', trans('credentials.you_have_been_suspended_from_resending_activation_emails'));
    }
});

$router->filter('throttle.reset', function ($route, $request) {
    // check if we've reached the rate limit, but don't hit the throttle yet
    // we can hit the throttle later on in the if validation passes
    if (!Throttle::check($request, 5, 30)) {
        return Redirect::route('account.reset')->withInput()
            ->with('error', trans('credentials.you_have_been_suspended_from_resetting_passwords'));
    }
});

$router->filter('throttle.register', function ($route, $request) {
    // check if we've reached the rate limit, but don't hit the throttle yet
    // we can hit the throttle later on in the if validation passes
    if (!Throttle::check($request, 5, 30)) {
        return Redirect::route('account.register')->withInput()
            ->with('error', trans('credentials.you_have_been_suspended_from_registration'));
    }
});
