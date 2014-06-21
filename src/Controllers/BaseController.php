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

use Illuminate\View\Factory;
use GrahamCampbell\Binput\Binput;
use GrahamCampbell\Credentials\Credentials;
use GrahamCampbell\Credentials\Providers\UserProvider;

/**
 * This is the base controller class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
abstract class BaseController extends AbstractController
{
    /**
     * The binput instance.
     *
     * @var \GrahamCampbell\Binput\Binput
     */
    protected $binput;

    /**
     * The user provider instance.
     *
     * @var \GrahamCampbell\Credentials\Providers\UserProvider
     */
    protected $userprovider;

    /**
     * The view factory instance.
     *
     * @var \Illuminate\View\Factory
     */
    protected $view;

    /**
     * Create a new instance.
     *
     * @param  \GrahamCampbell\Credentials\Credentials  $credentials
     * @param  \GrahamCampbell\Binput\Binput  $binput
     * @param  \GrahamCampbell\Credentials\Providers\UserProvider  $userprovider
     * @param  \Illuminate\View\Factory  $view
     * @return void
     */
    public function __construct(Credentials $credentials, Binput $binput, UserProvider $userprovider, Factory $view)
    {
        $this->binput = $binput;
        $this->userprovider = $userprovider;
        $this->view = $view;

        parent::__construct($credentials);
    }

    /**
     * Return the binput instance.
     *
     * @return \GrahamCampbell\Binput\Binput
     */
    public function getBinput()
    {
        return $this->binput;
    }

    /**
     * Return the user provider instance.
     *
     * @return \GrahamCampbell\Credentials\Providers\UserProvider
     */
    public function getUserProvider()
    {
        return $this->userprovider;
    }

    /**
     * Return the view factory instance.
     *
     * @return \Illuminate\View\Factory
     */
    public function getView()
    {
        return $this->view;
    }
}
