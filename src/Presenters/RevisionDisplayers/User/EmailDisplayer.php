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
        return 'Email Changed';
    }

    /**
     * Get the change description from the context of
     * the change being made to the current user.
     *
     * @return string
     */
    protected function current()
    {
        return $this->author().'changed your email address'.$this->details();
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
            return 'This user changed their email address'.$this->details();
        }

        return $this->author().'changed'.$this->user().'email address'.$this->details();
    }
}
