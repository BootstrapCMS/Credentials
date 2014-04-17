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

namespace GrahamCampbell\Credentials;

use Illuminate\Support\ServiceProvider;

/**
 * This is the credentials service provider class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
class CredentialsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('graham-campbell/credentials', 'graham-campbell/credentials', __DIR__);

        $this->setupViewer();

        include __DIR__.'/routes.php';
        include __DIR__.'/filters.php';
        include __DIR__.'/listeners.php';
    }

    /**
     * Setup the viewer class.
     *
     * @return void
     */
    protected function setupViewer()
    {
        $this->app->bindShared('viewer', function ($app) {
            $view = $app['view'];

            return new Classes\Viewer($view);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerUserProvider();
        $this->registerGroupProvider();
        $this->registerCredentials();
        $this->registerCommandSubscriber();

        $this->registerAccountController();
        $this->registerLoginController();
        $this->registerRegistrationController();
        $this->registerResetController();
        $this->registerResendController();
        $this->registerUserController();
    }

    /**
     * Register the user provider class.
     *
     * @return void
     */
    protected function registerUserProvider()
    {
        $this->app->bindShared('userprovider', function ($app) {
            $model = $app['config']['cartalyst/sentry::users.model'];
            $user = new $model();

            $validator = $app['validator'];

            return new Providers\UserProvider($user, $validator);
        });
    }

    /**
     * Register the group provider class.
     *
     * @return void
     */
    protected function registerGroupProvider()
    {
        $this->app->bindShared('groupprovider', function ($app) {
            $model = $app['config']['cartalyst/sentry::groups.model'];
            $group = new $model();

            $validator = $app['validator'];

            return new Providers\GroupProvider($group, $validator);
        });
    }

    /**
     * Register the credentials class.
     *
     * @return void
     */
    protected function registerCredentials()
    {
        $this->app->bindShared('credentials', function ($app) {
            $sentry = $app['sentry'];

            return new Classes\Credentials($sentry);
        });
    }

    /**
     * Register the command subscriber class.
     *
     * @return void
     */
    protected function registerCommandSubscriber()
    {
        $this->app->bindShared('GrahamCampbell\Credentials\Subscribers\CommandSubscriber', function ($app) {
            return new Subscribers\CommandSubscriber();
        });
    }

    /**
     * Register the account controller class.
     *
     * @return void
     */
    protected function registerAccountController()
    {
        $this->app->bind('GrahamCampbell\Credentials\Controllers\AccountController', function ($app) {
            $credentials = $app['credentials'];
            $viewer = $app['viewer'];
            $binput = $app['binput'];
            $userprovider = $app['userprovider'];

            return new Controllers\AccountController($credentials, $viewer, $binput, $userprovider);
        });
    }

    /**
     * Register the login controller class.
     *
     * @return void
     */
    protected function registerLoginController()
    {
        $this->app->bind('GrahamCampbell\Credentials\Controllers\LoginController', function ($app) {
            $credentials = $app['credentials'];
            $viewer = $app['viewer'];
            $binput = $app['binput'];
            $userprovider = $app['userprovider'];

            return new Controllers\LoginController($credentials, $viewer, $binput, $userprovider);
        });
    }

    /**
     * Register the registration controller class.
     *
     * @return void
     */
    protected function registerRegistrationController()
    {
        $this->app->bind('GrahamCampbell\Credentials\Controllers\RegistrationController', function ($app) {
            $credentials = $app['credentials'];
            $viewer = $app['viewer'];
            $binput = $app['binput'];
            $userprovider = $app['userprovider'];

            return new Controllers\RegistrationController($credentials, $viewer, $binput, $userprovider);
        });
    }

    /**
     * Register the reset controller class.
     *
     * @return void
     */
    protected function registerResetController()
    {
        $this->app->bind('GrahamCampbell\Credentials\Controllers\ResetController', function ($app) {
            $credentials = $app['credentials'];
            $viewer = $app['viewer'];
            $binput = $app['binput'];
            $userprovider = $app['userprovider'];

            return new Controllers\ResetController($credentials, $viewer, $binput, $userprovider);
        });
    }

    /**
     * Register the resend controller class.
     *
     * @return void
     */
    protected function registerResendController()
    {
        $this->app->bind('GrahamCampbell\Credentials\Controllers\ResendController', function ($app) {
            $credentials = $app['credentials'];
            $viewer = $app['viewer'];
            $binput = $app['binput'];
            $userprovider = $app['userprovider'];

            return new Controllers\ResendController($credentials, $viewer, $binput, $userprovider);
        });
    }

    /**
     * Register the user controller class.
     *
     * @return void
     */
    protected function registerUserController()
    {
        $this->app->bind('GrahamCampbell\Credentials\Controllers\UserController', function ($app) {
            $credentials = $app['credentials'];
            $viewer = $app['viewer'];
            $binput = $app['binput'];
            $userprovider = $app['userprovider'];

            return new Controllers\UserController($credentials, $viewer, $binput, $userprovider);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array(
            'userprovider',
            'groupprovider',
            'credentials',
            'viewer'
        );
    }
}
