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
 * This is the email displayer class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class EmailDisplayer extends AbstractDisplayer
{
    /**
     * Get the change title.
     *
     * @return string
     */
    public function title()
    {
        return trans('credentials.email_changed');
    }

    /**
     * Get the change description from the context of
     * the change being made to the current user.
     *
     * @return string
     */
    protected function current()
    {
        return trans('credentials.user_changed_your_email_address', ['user' => $this->author(), 'email' => $this->details()]);
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
            return trans('credentials.this_user_changed_their_email_address').$this->details();
        }

        return trans('credentials.user_changed_user_email_address', ['user1' => $this->author(), 'user2' => $this->user(), 'email' => $this->details()]);
    }
}
