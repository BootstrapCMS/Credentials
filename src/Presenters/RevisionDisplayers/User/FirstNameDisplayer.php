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
 * This is the first name displayer class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class FirstNameDisplayer extends AbstractDisplayer
{
    /**
     * Get the change title.
     *
     * @return string
     */
    public function title()
    {
        return 'Updated Name';
    }

    /**
     * Get the change description from the context of
     * the change being made to the current user.
     *
     * @return string
     */
    protected function current()
    {
        return $this->author().'changed your first name'.$this->details();
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
            return 'This user changed their first name'.$this->details();
        }

        return $this->author().'changed'.$this->user().'first name'.$this->details();
    }
}
