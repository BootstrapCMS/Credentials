<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

// check if the user is logged in and their access level
Route::filter('credentials', function ($route, $request, $value) {
    if (!Credentials::check()) {
        Log::info('User tried to access a page without being logged in', ['path' => $request->path()]);
        if (Request::ajax()) {
            throw new UnauthorizedHttpException('Action Requires Login');
        }

        return Redirect::guest(URL::route('account.login'))
            ->with('error', 'You must be logged in to perform that action.');
    }

    if (!Credentials::hasAccess($value)) {
        Log::warning(
            'User tried to access a page without permission',
            ['path' => $request->path(), 'permission' => $value]
        );
        throw new AccessDeniedHttpException(ucwords($value).' Permissions Are Required');
    }
});

Route::filter('throttle.sentry', function ($route, $request) {
    Credentials::getThrottleProvider()->enable();
});

Route::filter('throttle.login', function ($route, $request) {
    // check if we've reached the rate limit, but don't hit the throttle yet
    // we can hit the throttle later on in the if validation passes
    if (!Throttle::check($request, 10, 10)) {
        return Redirect::route('account.login')->withInput()
            ->with('error', 'You have made too many login requests. Please try again in 10 minutes.');
    }
});

Route::filter('throttle.activate', function ($route, $request) {
    // check if we've reached the rate limit, and hit the throttle
    // no validation is required, we should always hit the throttle
    if (!Throttle::attempt($request, 10, 10)) {
        return Redirect::route('account.login')->withInput()
            ->with('error', 'You have made too many activation requests. Please try again in 10 minutes.');
    }
});

Route::filter('throttle.resend', function ($route, $request) {
    // check if we've reached the rate limit, but don't hit the throttle yet
    // we can hit the throttle later on in the if validation passes
    if (!Throttle::check($request, 5, 30)) {
        return Redirect::route('account.resend')->withInput()
            ->with('error', 'Your have been suspended from resending activation emails. Please contact support.');
    }
});

Route::filter('throttle.reset', function ($route, $request) {
    // check if we've reached the rate limit, but don't hit the throttle yet
    // we can hit the throttle later on in the if validation passes
    if (!Throttle::check($request, 5, 30)) {
        return Redirect::route('account.reset')->withInput()
            ->with('error', 'Your have been suspended from resetting passwords. Please contact support.');
    }
});

Route::filter('throttle.register', function ($route, $request) {
    // check if we've reached the rate limit, but don't hit the throttle yet
    // we can hit the throttle later on in the if validation passes
    if (!Throttle::check($request, 5, 30)) {
        return Redirect::route('account.register')->withInput()
            ->with('error', 'Your have been suspended from registration. Please contact support.');
    }
});
