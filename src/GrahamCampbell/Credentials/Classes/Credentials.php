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

namespace GrahamCampbell\Credentials\Classes;

use Cartalyst\Sentry\Sentry;
use GrahamCampbell\Credentials\Providers\UserProvider;
use GrahamCampbell\Credentials\Providers\GroupProvider;

/**
 * This is the credentials class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
class Credentials
{
    /**
     * The cache of the check method.
     *
     * @var mixed
     */
    protected $check;

    /**
     * The sentry instance.
     *
     * @var \Cartalyst\Sentry\Sentry
     */
    protected $sentry;

    /**
     * The user provider instance.
     *
     * @var \GrahamCampbell\Credentials\Providers\UserProvider
     */
    protected $userprovider;

    /**
     * The group provider instance.
     *
     * @var \GrahamCampbell\Credentials\Providers\GroupProvider
     */
    protected $groupprovider;

    /**
     * Create a new instance.
     *
     * @param  \Cartalyst\Sentry\Sentry  $sentry
     * @return void
     */
    public function __construct(Sentry $sentry, UserProvider $userprovider, GroupProvider $groupprovider)
    {
        $this->sentry = $sentry;
        $this->userprovider = $userprovider;
        $this->groupprovider = $groupprovider;
    }

    /**
     * Call Sentry's check method or load of cached value.
     *
     * @return bool
     */
    public function check($cache = true)
    {
        if (is_null($this->check) || $cache === false) {
            $this->check = $this->sentry->check();
        }

        return $this->check();
    }

    /**
     * Dynamically pass all other methods to sentry.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->sentry, $method), $parameters);
    }
}
