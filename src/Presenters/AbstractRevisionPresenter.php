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

namespace GrahamCampbell\Credentials\Presenters;

use McCool\LaravelAutoPresenter\BasePresenter;
use GrahamCampbell\Credentials\Facades\Differ;
use GrahamCampbell\Credentials\Models\Revision;
use GrahamCampbell\Credentials\Facades\Credentials;

/**
 * This is the abstract revision presenter class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
abstract class AbstractRevisionPresenter extends BasePresenter
{
    use AuthorPresenterTrait, OwnerPresenterTrait;

    /**
     * Create a new instance.
     *
     * @param  \GrahamCampbell\Credentials\Models\Revision  $revision
     * @return void
     */
    public function __construct(Revision $revision)
    {
        $this->resource = $revision;
    }

    /**
     * Get the change title.
     *
     * @return string
     */
    public function title()
    {
        $method = camel_case($this->field()).'Title';
        return $this->$method();
    }

    /**
     * Get the change description.
     *
     * @return string
     */
    public function description()
    {
        $method = camel_case($this->field()).'Description';
        return $this->$method();
    }

    /**
     * Get the change field.
     *
     * @return string
     */
    public function field()
    {
        if (strpos($this->key, '_id')) {
            return str_replace('_id', '', $this->key);
        } else {
            return $this->key;
        }
    }

    /**
     * Get diff.
     *
     * @return string
     */
    public function diff()
    {
        return Differ::diff($this->resource->old_value, $this->resource->new_value);
    }

    /**
     * Get user name.
     *
     * @return string
     */
    public function userName()
    {
        if (Credentials::check()) {
            if (Credentials::getUser()->id == $this->getUserId()) {
                return 'You';
            }

            if (Credentials::hasAccess('admin')) {
                return $this->owner();
            }
        }

        return $this->author();
    }

    /**
     * Get the user id.
     *
     * @return int
     */
    protected function getUserId()
    {
        $user = $this->resource->user()
            ->cacheDriver('array')
            ->rememberForever()
            ->first(array('id'));

        return $user->id;
    }
}
