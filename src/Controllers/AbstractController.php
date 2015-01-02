<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Controllers;

use Illuminate\Routing\Controller;

/**
 * This is the abstract controller class.
 *
 * @author Graham Campbell <graham@mineuk.com>
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
        $this->beforeFilter('csrf', ['on' => 'post']);

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
