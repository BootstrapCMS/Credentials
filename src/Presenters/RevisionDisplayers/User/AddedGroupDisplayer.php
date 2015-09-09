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
 * This is the added group displayer class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class AddedGroupDisplayer extends AbstractDisplayer
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
        if (trim($this->author()) === trans('credentials::credentials.you')) {
            return trans('credentials::credentials.you_added_yourself_to_the_group', ['group_name' => $this->wrappedObject->new_value]);
        }

        return trans('credentials::credentials.someone_added_your_to_the_group', ['name' => $this->author(), 'group_name' => $this->wrappedObject->new_value]);
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
            return trans('credentials::credentials.this_user_added_themselves_to_the_group', ['group_name' => $this->wrappedObject->new_value]);
        }

        return trans('credentials::credentials.user_added_user_to_the_group', ['user1' => $this->author(), 'user1' => substr($this->user(), 0, -3), 'group_name' => $this->wrappedObject->new_value]);
    }
}
