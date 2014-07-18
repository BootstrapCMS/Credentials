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

namespace GrahamCampbell\Credentials\Presenters;

use Illuminate\Support\Facades\Html;

/**
 * This is the user revision presenter class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
class UserRevisionPresenter extends AbstractRevisionPresenter
{
    protected function emailTitle()
    {
        return 'Email Changed';
    }

    protected function emailDescription()
    {
        if ($name = $this->userName() === 'You') {
            return 'You changed your email address from "'.$this->resource->old_value.'" to "'.$this->resource->new_value.'".';
        }

        return 'They changed their email adddress from "'.$this->resource->old_value.'" to "'.$this->resource->new_value.'".';
    }

    protected function passwordTitle()
    {
        return 'Password Changed';
    }

    protected function passwordDescription()
    {
        if ($name = $this->userName() === 'You') {
            return 'You changed your password.';
        }

        return 'They changed their password.';
    }

    protected function activatedTitle()
    {
        'Account Activated';
    }

    protected function activatedDescription()
    {
        if ($name = $this->userName() === 'You') {
            return 'You activated your account.';
        }

        return 'They activated their account.';
    }

    protected function lastLoginTitle()
    {
        return 'Login Event';
    }

    protected function lastLoginDescription()
    {
        if ($name = $this->userName() === 'You') {
            return 'You logged into your account.';
        }

        return 'They logged into their account.';
    }

    protected function firstNameTitle()
    {
        return 'Fist Name Changed';
    }

    protected function firstNameDescription()
    {
        if ($name = $this->userName() === 'You') {
            return 'You changed your first name from "'.$this->resource->old_value.'" to "'.$this->resource->new_value.'".';
        }

        return 'They changed their first name from "'.$this->resource->old_value.'" to "'.$this->resource->new_value.'".';
    }

    protected function lastNameTitle()
    {
        return 'Last Name Changed';
    }

    protected function lastNameDescription()
    {
        if ($name = $this->userName() === 'You') {
            return 'You changed your last name from "'.$this->resource->old_value.'" to "'.$this->resource->new_value.'".';
        }

        return 'They changed their last name from "'.$this->resource->old_value.'" to "'.$this->resource->new_value.'".';
    }
}
