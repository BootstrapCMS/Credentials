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
        $this->app['userprovider'] = $this->app->share(function ($app) {
            $model = $app['config']['cartalyst/sentry::users.model'];
            return new Providers\UserProvider($model);
        });

        $this->app['groupprovider'] = $this->app->share(function ($app) {
            $model = $app['config']['cartalyst/sentry::groups.model'];
            return new Providers\GroupProvider($model);
        });

        $this->app['credentials'] = $this->app->share(function ($app) {
            return new Classes\Credentials($app['sentry'], $app['userprovider'], $app['groupprovider']);
        });

        $this->app['viewer'] = $this->app->share(function ($app) {
            return new Classes\Viewer($app['view'], $app['sentry']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('userprovider', 'groupprovider', 'credentials', 'viewer');
    }
}
