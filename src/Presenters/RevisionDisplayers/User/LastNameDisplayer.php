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

/**
 * This is the last name displayer class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class LastNameDisplayer extends AbstractDisplayer
{
    /**
     * Get the change title.
     *
     * @return string
     */
    public function title()
    {
        return trans('credentials::credentials.updated_last_name');
    }

    /**
     * Get the change description from the context of
     * the change being made to the current user.
     *
     * @return string
     */
    protected function current()
    {
        return $this->author().trans('credentials::credentials.changed_your_last_name').$this->details();
    }

    /**
     * Get the change description from the context of
     * the change not being made to the current user.
     *
     * @return string
     */
    protected function external()
    {
        if ($this->wasActualUser()) {
            return trans('credentials::credentials.this_user_changed_their_last_name').$this->details();
        }

        return trans('credentials::credentials.user_changed_user_last_name', ['user1' => $this->author(), 'user2' => $this->user()]).$this->details();
    }
}
