<?php

/**
 * This file is part of Laravel Credentials by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace GrahamCampbell\Credentials\Presenters\RevisionDisplayers\User;

/**
 * This is the password displayer class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
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
            'You reset your password.';
        }

        return $this->author() . 'changed your password.';
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
            return 'The user changed their password.';
        }

        if ($this->author() === ' ') {
            return 'The user reset their password.';
        }

        return $this->author() . 'reset the user\'s password.';
    }
}
