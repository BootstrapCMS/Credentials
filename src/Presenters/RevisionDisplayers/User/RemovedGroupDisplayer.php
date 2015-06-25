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
        return 'Group Membership Changed';
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
            return 'You removed yourself from the "'.$this->wrappedObject->new_value.'" group.';
        }

        return $this->author().'removed you from the "'.$this->wrappedObject->new_value.'" group.';
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
            return 'This user removed themselves from the "'.$this->wrappedObject->new_value.'" group.';
        }

        return $this->author().'removed'.substr($this->user(), 0, -3).' from the "'.$this->wrappedObject->new_value.'" group.';
    }
}
