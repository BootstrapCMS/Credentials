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

namespace GrahamCampbell\Credentials\Presenters\RevisionDisplayers\User;

use GrahamCampbell\Credentials\Facades\Credentials;
use GrahamCampbell\Credentials\Presenters\RevisionDisplayers\AbstractRevisionDisplayer;
use GrahamCampbell\Credentials\Presenters\RevisionDisplayers\RevisionDisplayerInterface;

/**
 * This is the abstract displayer class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
abstract class AbstractDisplayer extends AbstractRevisionDisplayer implements RevisionDisplayerInterface
{
    /**
     * Get the change description.
     *
     * @return string
     */
    public function description()
    {
        if ($this->isCurrentUser()) {
            return $this->current();
        }

        return $this->external();
    }

    /**
     * Get the change description from the context of
     * the change being made to the current user.
     *
     * @return string
     */
    abstract protected function current();

    /**
     * Get the change description from the context of
     * the change not being made to the current user.
     *
     * @return string
     */
    abstract protected function external();

    /**
     * Was the action by the actual user?
     *
     * @return bool
     */
    protected function wasActualUser()
    {
        return ($this->resource->user_id == $this->userId() || !$this->resource->user_id);
    }

    /**
     * Was the action by the current user?
     *
     * @return bool
     */
    protected function wasCurrentUser()
    {
        return $this->presenter->wasByCurrentUser();
    }

    /**
     * Is the current user's account?
     *
     * @return bool
     */
    protected function isCurrentUser()
    {
        return (Credentials::check() && Credentials::getUser()->id == $this->userId());
    }

    /**
     * Get the user id.
     *
     * @return int
     */
    protected function userId()
    {
        $user = $this->resource->revisionable()
            ->cacheDriver('array')
            ->rememberForever()
            ->first(array('id'));

        return $user->id;
    }

    /**
     * Get the author details.
     *
     * @return string
     */
    protected function author()
    {
        if ($this->wasCurrentUser() || !$this->resource->user_id) {
            return 'You ';
        }

        return $this->presenter->author() . ' ';
    }

    /**
     * Get the change details.
     *
     * @return string
     */
    protected function details()
    {
        return ' from "'.$this->resource->old_value.'" to "'.$this->resource->new_value.'".';
    }
}
