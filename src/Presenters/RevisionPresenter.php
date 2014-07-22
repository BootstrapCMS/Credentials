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

use GrahamCampbell\Credentials\Facades\Credentials;
use GrahamCampbell\Credentials\Facades\Differ;
use GrahamCampbell\Credentials\Models\Revision;
use McCool\LaravelAutoPresenter\BasePresenter;

/**
 * This is the revision presenter class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
class RevisionPresenter extends BasePresenter
{
    use AuthorPresenterTrait;

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
        $class = $this->getDisplayerClass();

        return with(new $class($this))->title();
    }

    /**
     * Get the change description.
     *
     * @return string
     */
    public function description()
    {
        $class = $this->getDisplayerClass();

        return with(new $class($this))->description();
    }

    /**
     * Get relevant displayer class.
     *
     * @return string
     */
    protected function getDisplayerClass()
    {
        $class = $this->resource->revisionable_type;
        $shortArray = explode('\\', $class);
        $short = end($shortArray);
        $field = studly_case($this->field());

        $temp = str_replace($short, 'RevisionDisplayers\\'.$short.'\\'.$field.'Displayer', $class);

        return str_replace('Model', 'Presenter', $temp);
    }

    /**
     * Get the change field.
     *
     * @return string
     */
    public function field()
    {
        if (strpos($this->resource->key, '_id')) {
            return str_replace('_id', '', $this->resource->key);
        }

        return $this->resource->key;
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
     * Was the event invoked by the current user?
     *
     * @return bool
     */
    public function wasByCurrentUser()
    {
        return (Credentials::check() && Credentials::getUser()->id == $this->resource->user_id);
    }
}
