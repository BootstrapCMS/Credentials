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

use GrahamCampbell\Throttle\Facades\Throttle;
use Illunimate\Support\Facades\Redirect;

$router->filter('throttle.login', function ($route, $request) {
    // check if we've reached the rate limit, but don't hit the throttle yet
    // we can hit the throttle later on in the if validation passes
    if (!Throttle::check($request, 10, 10)) {
        return Redirect::route('account.login')->withInput()
            ->with('error', 'You have made too many login requests. Please try again in 10 minutes.');
    }
});

$router->filter('throttle.activate', function ($route, $request) {
    // check if we've reached the rate limit, and hit the throttle
    // no validation is required, we should always hit the throttle
    if (!Throttle::attempt($request, 10, 10)) {
        return Redirect::route('account.login')->withInput()
            ->with('error', 'You have made too many activation requests. Please try again in 10 minutes.');
    }
});

$router->filter('throttle.resend', function ($route, $request) {
    // check if we've reached the rate limit, but don't hit the throttle yet
    // we can hit the throttle later on in the if validation passes
    if (!Throttle::check($request, 5, 30)) {
        return Redirect::route('account.resend')->withInput()
            ->with('error', 'Your have been suspended from resending activation emails. Please contact support.');
    }
});

$router->filter('throttle.reset', function ($route, $request) {
    // check if we've reached the rate limit, but don't hit the throttle yet
    // we can hit the throttle later on in the if validation passes
    if (!Throttle::check($request, 5, 30)) {
        return Redirect::route('account.reset')->withInput()
            ->with('error', 'Your have been suspended from resetting passwords. Please contact support.');
    }
});

$router->filter('throttle.register', function ($route, $request) {
    // check if we've reached the rate limit, but don't hit the throttle yet
    // we can hit the throttle later on in the if validation passes
    if (!Throttle::check($request, 5, 30)) {
        return Redirect::route('account.register')->withInput()
            ->with('error', 'Your have been suspended from registration. Please contact support.');
    }
});
