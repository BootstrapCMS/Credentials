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

namespace GrahamCampbell\Credentials;

use Illuminate\Support\ServiceProvider;
use SebastianBergmann\Diff\Differ;

/**
 * This is the credentials service provider class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md> Apache 2.0
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
        $this->registerRevisionRepository();
        $this->registerUserRepository();
        $this->registerGroupRepository();
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
        $this->app->singleton('differ', function ($app) {
            return new Differ();
        });

        $this->app->alias('differ', 'SebastianBergmann\Diff\Differ');
    }

    /**
     * Register the revision repository class.
     *
     * @return void
     */
    protected function registerRevisionRepository()
    {
        $this->app->singleton('revisionrepository', function ($app) {
            $model = $app['config']['graham-campbell/credentials::revision'];
            $revision = new $model();

            $validator = $app['validator'];

            return new Repositories\RevisionRepository($revision, $validator);
        });

        $this->app->alias('revisionrepository', 'GrahamCampbell\Credentials\Repositories\RevisionRepository');
    }

    /**
     * Register the user repository class.
     *
     * @return void
     */
    protected function registerUserRepository()
    {
        $this->app->singleton('userrepository', function ($app) {
            $model = $app['config']['cartalyst/sentry::users.model'];
            $user = new $model();

            $validator = $app['validator'];

            return new Repositories\UserRepository($user, $validator);
        });

        $this->app->alias('userrepository', 'GrahamCampbell\Credentials\Repositories\UserRepository');
    }

    /**
     * Register the group repository class.
     *
     * @return void
     */
    protected function registerGroupRepository()
    {
        $this->app->singleton('grouprepository', function ($app) {
            $model = $app['config']['cartalyst/sentry::groups.model'];
            $group = new $model();

            $validator = $app['validator'];

            return new Repositories\GroupRepository($group, $validator);
        });

        $this->app->alias('grouprepository', 'GrahamCampbell\Credentials\Repositories\GroupRepository');
    }

    /**
     * Register the credentials class.
     *
     * @return void
     */
    protected function registerCredentials()
    {
        $this->app->singleton('credentials', function ($app) {
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
        $this->app->singleton('GrahamCampbell\Credentials\Subscribers\CommandSubscriber', function ($app) {
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
        $this->app->bind('GrahamCampbell\Credentials\Http\Controllers\AccountController', function ($app) {
            return new Http\Controllers\AccountController();
        });
    }

    /**
     * Register the login controller class.
     *
     * @return void
     */
    protected function registerLoginController()
    {
        $this->app->bind('GrahamCampbell\Credentials\Http\Controllers\LoginController', function ($app) {
            $throttler = $app['throttle']->get($app['request'], 10, 10);

            return new Http\Controllers\LoginController($throttler);
        });
    }

    /**
     * Register the registration controller class.
     *
     * @return void
     */
    protected function registerRegistrationController()
    {
        $this->app->bind('GrahamCampbell\Credentials\Http\Controllers\RegistrationController', function ($app) {
            $throttler = $app['throttle']->get($app['request'], 5, 30);

            return new Http\Controllers\RegistrationController($throttler);
        });
    }

    /**
     * Register the reset controller class.
     *
     * @return void
     */
    protected function registerResetController()
    {
        $this->app->bind('GrahamCampbell\Credentials\Http\Controllers\ResetController', function ($app) {
            $throttler = $app['throttle']->get($app['request'], 5, 30);

            return new Http\Controllers\ResetController($throttler);
        });
    }

    /**
     * Register the resend controller class.
     *
     * @return void
     */
    protected function registerActivationController()
    {
        $this->app->bind('GrahamCampbell\Credentials\Http\Controllers\ActivationController', function ($app) {
            $throttler = $app['throttle']->get($app['request'], 5, 30);

            return new Http\Controllers\ActivationController($throttler);
        });
    }

    /**
     * Register the user controller class.
     *
     * @return void
     */
    protected function registerUserController()
    {
        $this->app->bind('GrahamCampbell\Credentials\Http\Controllers\UserController', function ($app) {
            return new Http\Controllers\UserController();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'differ',
            'revisionrepository',
            'userrepository',
            'grouprepository',
            'credentials',
        ];
    }
}
