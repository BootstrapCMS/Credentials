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
use SebastianBergmann\Diff\Differ;

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

        $this->setupBlade();

        include __DIR__.'/routes.php';
        include __DIR__.'/filters.php';
        include __DIR__.'/listeners.php';
    }

    /**
     * Setup the blade compiler class.
     *
     * @return void
     */
    protected function setupBlade()
    {
        $blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

        $blade->extend(function ($value, $compiler) {
            $pattern = $compiler->createMatcher('auth');
            $replace = '$1<?php if (\GrahamCampbell\Credentials\Facades\Credentials::check() && \GrahamCampbell\Credentials\Facades\Credentials::hasAccess$2): ?>';
            return preg_replace($pattern, $replace, $value);
        });

        $blade->extend(function ($value, $compiler) {
            $pattern = $compiler->createPlainMatcher('endauth');
            $replace = '$1<?php endif; ?>$2';
            return preg_replace($pattern, $replace, $value);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerDiffer();
        $this->registerUserProvider();
        $this->registerGroupProvider();
        $this->registerCredentials();
        $this->registerCommandSubscriber();

        $this->registerAccountController();
        $this->registerLoginController();
        $this->registerRegistrationController();
        $this->registerResetController();
        $this->registerActivationController();
        $this->registerUserController();
    }

    /**
     * Register the differ class.
     *
     * @return void
     */
    protected function registerDiffer()
    {
        $this->app->bindShared('differ', function ($app) {
            return new Differ();
        });

        $this->app->alias('differ', 'SebastianBergmann\Diff\Differ');
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

        $this->app->alias('userprovider', 'GrahamCampbell\Credentials\Providers\UserProvider');
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

        $this->app->alias('groupprovider', 'GrahamCampbell\Credentials\Providers\GroupProvider');
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
            $decorator = $app->make('McCool\LaravelAutoPresenter\PresenterDecorator');

            return new Credentials($sentry, $decorator);
        });

        $this->app->alias('credentials', 'GrahamCampbell\Credentials\Credentials');
    }

    /**
     * Register the command subscriber class.
     *
     * @return void
     */
    protected function registerCommandSubscriber()
    {
        $this->app->bindShared('GrahamCampbell\Credentials\Subscribers\CommandSubscriber', function ($app) {
            $force = trait_exists('Illuminate\Support\Traits\MacroableTrait');

            return new Subscribers\CommandSubscriber($force);
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
            return new Controllers\AccountController();
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
            $throttler = $app['throttle']->get($app['request'], 10, 10);

            return new Controllers\LoginController($throttler);
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
            $throttler = $app['throttle']->get($app['request'], 5, 30);

            return new Controllers\RegistrationController($throttler);
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
            $throttler = $app['throttle']->get($app['request'], 5, 30);

            return new Controllers\ResetController($throttler);
        });
    }

    /**
     * Register the resend controller class.
     *
     * @return void
     */
    protected function registerActivationController()
    {
        $this->app->bind('GrahamCampbell\Credentials\Controllers\ActivationController', function ($app) {
            $throttler = $app['throttle']->get($app['request'], 5, 30);

            return new Controllers\ActivationController($throttler);
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
            return new Controllers\UserController();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return array(
            'differ',
            'userprovider',
            'groupprovider',
            'credentials'
        );
    }
}
