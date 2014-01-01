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

namespace GrahamCampbell\Credentials\Controllers;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use GrahamCampbell\Core\Controllers\AbstractController as Controller;

/**
 * This is the abstract controller class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/develop/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
abstract class AbstractController extends Controller
{
    /**
     * A list of methods protected by user permissions.
     *
     * @var array
     */
    protected $users = array();

    /**
     * A list of methods protected by mod permissions.
     *
     * @var array
     */
    protected $mods = array();

    /**
     * A list of methods protected by admin permissions.
     *
     * @var array
     */
    protected $admins = array();

    /**
     * Constructor (setup protection and permissions).
     *
     * @return void
     */
    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));

        Sentry::getThrottleProvider()->enable();

        $this->beforeFilter('credentials:user', array('only' => $this->users));
        $this->beforeFilter('credentials:mod', array('only' => $this->mods));
        $this->beforeFilter('credentials:admin', array('only' => $this->admins));
    }

    /**
     * Set the permission.
     *
     * @pram  string  $action
     * @pram  string  $permission
     * @return void
     */
    protected function setPermission($action, $permission)
    {
        $this->{$permission.'s'}[] = $action;
    }

    /**
     * Set the permissions.
     *
     * @pram  array  $permissions
     * @return void
     */
    protected function setPermissions($permissions)
    {
        foreach ($permissions as $action => $permission) {
            $this->setPermission($action, $permission);
        }
    }

    /**
     * Set the user id.
     *
     * @return int
     */
    protected function getUserId()
    {
        if (Sentry::getUser()) {
            return Sentry::getUser()->getId();
        } else {
            return 1;
        }
    }
}
