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
use GrahamCampbell\Credentials\Models\Relations\Common\RevisionableTrait;
use GrahamCampbell\Credentials\Models\Relations\Interfaces\RevisionableInterface;
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
class Throttle extends SentryThrottle implements BaseModelInterface, RevisionableInterface
{
    use BaseModelTrait, RevisionableTrait;

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
     * The revisionable columns.
     *
     * @var array
     */
    protected $keepRevisionOf = array('last_attempt_at', 'suspended_at');

    /**
     * Should we only track updates?
     *
     * @var bool
     */
    protected $onlyTrackUpdates = true;

    /**
     * Should we track null updates?
     *
     * @var bool
     */
    protected $trackNullUpdates = false;

    /**
     * Get the custom model id.
     *
     * @var int
     */
    protected function getCustomKey()
    {
        return $this['user_id'];
    }

    /**
     * Get the custom model type.
     *
     * @var string
     */
    protected function getCustomType()
    {
        return Config::get('cartalyst/sentry::users.model');
    }
}
