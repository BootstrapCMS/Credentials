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

use GrahamCampbell\Credentials\Presenters\RevisionDisplayers\RevisionDisplayerInterface;

/**
 * This is the last name displayer class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
class LastNameDisplayer extends AbstractDisplayer implements RevisionDisplayerInterface
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
     * Get the change description.
     *
     * @return string
     */
    public function description()
    {
        if ($this->isCurrentUser()) {
            if ($this->wasCurrentUser()) {
                return 'You changed your last name' . $this->details();
            } else {
                return $this->author() . 'changed your last name' . $this->details();
            }
        } else {
            if ($this->wasActualUser()) {
                return 'The user changed their last name' . $this->details();
            } else {
                if ($this->wasCurrentUser()) {
                    return 'You changed the user\'s last name' . $this->details();
                } else {
                    return $this->author() . 'changed the user\'s last name' . $this->details();
                }
            }
        }
    }
}
