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

namespace GrahamCampbell\Credentials\Models;

use Cartalyst\Sentry\Throttling\Eloquent\Throttle as SentryThrottle;
use DateTime;
use GrahamCampbell\Credentials\Facades\RevisionProvider;
use GrahamCampbell\Database\Models\Common\BaseModelTrait;
use GrahamCampbell\Database\Models\Interfaces\BaseModelInterface;
use Illuminate\Support\Facades\Config;

/**
 * This is the throttle model class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md> Apache 2.0
 */
class Throttle extends SentryThrottle implements BaseModelInterface
{
    use BaseModelTrait;

    /**
     * The table the throttles are stored in.
     *
     * @var string
     */
    protected $table = 'throttle';

    /**
     * The model name.
     *
     * @var string
     */
    public static $name = 'throttle';

    /**
     * Add a new login attempt.
     *
     * @return void
    */
    public function addLoginAttempt()
    {
        RevisionProvider::create(array(
            'revisionable_type' => Config::get('cartalyst/sentry::users.model'),
            'revisionable_id'   => $this['user_id'],
            'key'               => 'last_attempt_at',
            'old_value'         => $this['last_attempt_at'],
            'new_value'         => new DateTime(),
            'user_id'           => null
        ));

        parent::addLoginAttempt();
    }

    /**
     * Suspend the user associated with the throttle.
     *
     * @return void
    */
    public function suspend()
    {
        RevisionProvider::create(array(
            'revisionable_type' => Config::get('cartalyst/sentry::users.model'),
            'revisionable_id'   => $this['user_id'],
            'key'               => 'suspended_at',
            'old_value'         => $this['suspended_at'],
            'new_value'         => new DateTime(),
            'user_id'           => null
        ));

        parent::suspend();
    }
}
