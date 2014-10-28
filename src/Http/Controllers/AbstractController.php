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

namespace GrahamCampbell\Credentials\Http\Controllers;

use Illuminate\Routing\Controller;

/**
 * This is the abstract controller class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md> Apache 2.0
 */
abstract class AbstractController extends Controller
{
    /**
     * A list of methods protected by user permissions.
     *
     * @var string[]
     */
    protected $users = [];

    /**
     * A list of methods protected by mod permissions.
     *
     * @var string[]
     */
    protected $mods = [];

    /**
     * A list of methods protected by admin permissions.
     *
     * @var string[]
     */
    protected $admins = [];

    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->beforeFilter('credentials:user', ['only' => $this->users]);
        $this->beforeFilter('credentials:mod', ['only' => $this->mods]);
        $this->beforeFilter('credentials:admin', ['only' => $this->admins]);
    }

    /**
     * Set the permission.
     *
     * @param string $action
     * @param string $permission
     *
     * @return void
     */
    protected function setPermission($action, $permission)
    {
        $this->{$permission.'s'}[] = $action;
    }

    /**
     * Set the permissions.
     *
     * @param string[] $permissions
     *
     * @return void
     */
    protected function setPermissions($permissions)
    {
        foreach ($permissions as $action => $permission) {
            $this->setPermission($action, $permission);
        }
    }
}
