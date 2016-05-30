<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Presenters\RevisionDisplayers\User;

use GrahamCampbell\Credentials\Presenters\RevisionDisplayers\AbstractRevisionDisplayer;
use GrahamCampbell\Credentials\Presenters\RevisionDisplayers\RevisionDisplayerInterface;

/**
 * This is the abstract displayer class.
 *
 * @author Graham Campbell <graham@alt-three.com>
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
        return $this->wrappedObject->user_id == $this->wrappedObject->revisionable_id || !$this->wrappedObject->user_id;
    }

    /**
     * Is the current user's account?
     *
     * @return bool
     */
    protected function isCurrentUser()
    {
        return $this->credentials->check() && $this->credentials->getUser()->id == $this->wrappedObject->revisionable_id;
    }

    /**
     * Get the author details.
     *
     * @return string
     */
    protected function author()
    {
        if ($this->presenter->wasByCurrentUser() || !$this->wrappedObject->user_id) {
            return 'You ';
        }

        if (!$this->wrappedObject->security) {
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
        if ($this->wrappedObject->security) {
            return ' this user\'s ';
        }

        $user = $this->wrappedObject->revisionable()->withTrashed()->first(['first_name', 'last_name']);

        return ' '.$user->first_name.' '.$user->last_name.'\'s ';
    }
}
