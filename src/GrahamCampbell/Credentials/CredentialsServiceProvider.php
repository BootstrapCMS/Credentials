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
 * @copyright  Copyright 2013 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/develop/LICENSE.md
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
        $this->package('graham-campbell/credentials');

        include __DIR__.'/../../routes.php';
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
    }

    /**
     * Register the user provider class.
     *
     * @return void
     */
    protected function registerUserProvider()
    {
        $this->app->bindShared('userprovider', function ($app) {
            $user = $app['config']['cartalyst/sentry::users.model'];

            return new Providers\UserProvider($user);
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
            $group = $app['config']['cartalyst/sentry::groups.model'];

            return new Providers\GroupProvider($group);
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
            $userprovider = $app['userprovider'];
            $groupprovider = $app['groupprovider'];

            return new Classes\Credentials($sentry, $userprovider, $groupprovider);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('userprovider', 'groupprovider', 'credentials');
    }
}
