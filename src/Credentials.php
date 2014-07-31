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

use Cartalyst\Sentry\Sentry;
use McCool\LaravelAutoPresenter\PresenterDecorator;

/**
 * This is the credentials class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md> Apache 2.0
 */
class Credentials
{
    /**
     * The cache of the check method.
     *
     * @var mixed
     */
    protected $cache;

    /**
     * The sentry instance.
     *
     * @var \Cartalyst\Sentry\Sentry
     */
    protected $sentry;

    /**
     * The decorator instance.
     *
     * @var \McCool\LaravelAutoPresenter\PresenterDecorator
     */
    protected $decorator;

    /**
     * Create a new instance.
     *
     * @param \Cartalyst\Sentry\Sentry                        $sentry
     * @param \McCool\LaravelAutoPresenter\PresenterDecorator $decorator
     *
     * @return void
     */
    public function __construct(Sentry $sentry, PresenterDecorator $decorator)
    {
        $this->sentry = $sentry;
        $this->decorator = $decorator;
    }

    /**
     * Call Sentry's check method or load of cached value.
     *
     * @return bool
     */
    public function check()
    {
        if ($this->cache === null) {
            $this->cache = $this->sentry->check();
        }

        return $this->cache;
    }

    /**
     * Get the decorated current user.
     *
     * @return \GrahamCampbell\Credentials\Presenters\UserPresenter
     */
    public function getDecoratedUser()
    {
        if ($user = $this->sentry->getUser()) {
            return $this->decorator->decorate($user);
        }
    }

    /**
     * Dynamically pass all other methods to sentry.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->sentry, $method), $parameters);
    }
}
