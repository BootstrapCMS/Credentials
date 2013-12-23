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
use Illuminate\View\Environment;

/**
 * This is the view class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/develop/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
class Viewer
{
    /**
     * The view instance.
     *
     * @var \Illuminate\View\Environment
     */
    protected $view;

    /**
     * The sentry instance.
     *
     * @var \Cartalyst\Sentry\Sentry
     */
    protected $sentry;

    /**
     * Constructor (setup protection and permissions).
     *
     * @param  \Illuminate\View\Environment  $view
     * @param  \Cartalyst\Sentry\Sentry  $sentry
     * @return void
     */
    public function __construct(Environment $view, Sentry $sentry)
    {
        $this->view = $view;
        $this->sentry = $sentry;
    }

    /**
     * Get a evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  bool    $admin
     * @return \Illuminate\View\View
     */
    public function make($view, $data = array(), $admin = false)
    {
        if ($this->sentry->check()) {
            $this->events->fire('viewer.make', array(array('View' => $view, 'User' => true, 'Admin' => $admin)));
        } else {
            $this->events->fire('viewer.make', array(array('View' => $view, 'User' => false, 'Admin' => $admin)));
        }

        return $this->view->make($view, $data);
    }
}
