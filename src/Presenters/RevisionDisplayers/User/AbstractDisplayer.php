<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Presenters\RevisionDisplayers\User;

use GrahamCampbell\Credentials\Facades\Credentials;
use GrahamCampbell\Credentials\Presenters\RevisionDisplayers\AbstractRevisionDisplayer;
use GrahamCampbell\Credentials\Presenters\RevisionDisplayers\RevisionDisplayerInterface;

/**
 * This is the abstract displayer class.
 *
 * @author Graham Campbell <graham@mineuk.com>
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
        return ($this->resource->user_id == $this->resource->revisionable_id || !$this->resource->user_id);
    }

    /**
     * Is the current user's account?
     *
     * @return bool
     */
    protected function isCurrentUser()
    {
        return (Credentials::check() && Credentials::getUser()->id == $this->resource->revisionable_id);
    }

    /**
     * Get the author details.
     *
     * @return string
     */
    protected function author()
    {
        if ($this->presenter->wasByCurrentUser() || !$this->resource->user_id) {
            return 'You ';
        }

        if (!$this->resource->security) {
            return 'This user ';
        }

        return $this->presenter->author().' ';
    }

    /**
     * Get the user details.
     *
     * @return string
     */
    protected function user()
    {
        if ($this->resource->security) {
            return ' this user\'s ';
        }

        $user = $this->resource->revisionable()->withTrashed()
        ->cacheDriver('array')->rememberForever()
        ->first(['first_name', 'last_name']);

        return ' '.$user->first_name.' '.$user->last_name.'\'s ';
    }
}
