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
 * This is the password displayer class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class PasswordDisplayer extends AbstractDisplayer
{
    /**
     * Get the change title.
     *
     * @return string
     */
    public function title()
    {
        return 'Password Changed';
    }

    /**
     * Get the change description from the context of
     * the change being made to the current user.
     *
     * @return string
     */
    protected function current()
    {
        if ($this->author() === ' ') {
            return trans('credentials::credentials.you_reset_your_password');
        }

        return $this->author().trans('credentials::credentials.changed_your_password');
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
            return trans('credentials::credentials.this_user_changed_their_password');
        }

        return trans('credentials::credentials.user_changed_user_password', ['user1' => $this->author(), 'user2' => $this->user()]);
    }
}
