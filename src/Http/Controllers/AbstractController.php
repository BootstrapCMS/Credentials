<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Http\Controllers;

use GrahamCampbell\Credentials\Http\Middleware\Auth\Admin;
use GrahamCampbell\Credentials\Http\Middleware\Auth\Mod;
use GrahamCampbell\Credentials\Http\Middleware\Auth\User;
use Illuminate\Routing\Controller;

/**
 * This is the abstract controller class.
 *
 * @author Graham Campbell <graham@alt-three.com>
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
        if ($this->users) {
            $this->middleware(User::class, ['only' => $this->users]);
        }

        if ($this->mods) {
            $this->middleware(Mod::class, ['only' => $this->mods]);
        }

        if ($this->admins) {
            $this->middleware(Admin::class, ['only' => $this->admins]);
        }
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
