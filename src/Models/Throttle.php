<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Models;

use Cartalyst\Sentry\Throttling\Eloquent\Throttle as SentryThrottle;
use DateTime;
use GrahamCampbell\Credentials\Facades\RevisionRepository;
use Illuminate\Support\Facades\Config;

/**
 * This is the throttle model class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class Throttle extends SentryThrottle
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
        RevisionRepository::create([
            'revisionable_type' => Config::get('sentry.users.model'),
            'revisionable_id'   => $this['user_id'],
            'key'               => 'last_attempt_at',
            'old_value'         => $this['last_attempt_at'],
            'new_value'         => new DateTime(),
            'user_id'           => null,
        ]);

        parent::addLoginAttempt();
    }

    /**
     * Suspend the user associated with the throttle.
     *
     * @return void
     */
    public function suspend()
    {
        RevisionRepository::create([
            'revisionable_type' => Config::get('sentry.users.model'),
            'revisionable_id'   => $this['user_id'],
            'key'               => 'suspended_at',
            'old_value'         => $this['suspended_at'],
            'new_value'         => new DateTime(),
            'user_id'           => null,
        ]);

        parent::suspend();
    }
}
