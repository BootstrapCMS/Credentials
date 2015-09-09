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
 * This is the removed group displayer class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class RemovedGroupDisplayer extends AbstractDisplayer
{
    /**
     * Get the change title.
     *
     * @return string
     */
    public function title()
    {
        return trans('credentials::credentials.group_membership_changed');
    }

    /**
     * Get the change description from the context of
     * the change being made to the current user.
     *
     * @return string
     */
    protected function current()
    {
        if ($this->author() === 'You ') {
            return trans('credentials::credentials.you_removed_yourself_from_group', ['group' => $this->wrappedObject->new_value]);
        }

        return trans('credentials::credentials.someone_removed_yourself_from_group', ['user' => $this->author(), 'group' => $this->wrappedObject->new_value]);
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
            return trans('credentials::credentials.this_user_removed_themselves_from_group', ['group' => $this->wrappedObject->new_value]);
        }

        return trans('credentials::credentials.user_removed_user_from_group', ['user1' => $this->author(), 'user2' => substr($this->user(), 0, -3), 'group' => $this->wrappedObject->new_value]);
    }
}
