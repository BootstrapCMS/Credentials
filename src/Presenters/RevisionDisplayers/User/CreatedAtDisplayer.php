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
 * This is the created at displayer class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class CreatedAtDisplayer extends AbstractDisplayer
{
    /**
     * Get the change title.
     *
     * @return string
     */
    public function title()
    {
        return trans('credentials::credentials.account_created');
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
            return trans('credentials::credentials.you_created_your_account');
        }

        return $this->author().trans('credentials::credentials.created_your_account');
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
            return trans('credentials::credentials.this_user_created_their_account');
        }

        return trans('credentials::credentials.user_created_user_account', ['user1' => $this->author(), 'user2' => $this->user()]);
    }
}
