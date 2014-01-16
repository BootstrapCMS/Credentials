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

use Illuminate\View\Environment;
use GrahamCampbell\Viewer\Classes\Viewer as BaseViewer;

/**
 * This is the view class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
class Viewer extends BaseViewer
{
    /**
     * Create a new instance.
     *
     * @param  \Illuminate\View\Environment  $view
     * @return void
     */
    public function __construct(Environment $view)
    {
        parent::__construct($view);
    }

    /**
     * Get a evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  string  $type
     * @return \Illuminate\View\View
     */
    public function make($view, array $data = array(), $type = 'default')
    {
        return parent::make($view, $data);
    }
}
