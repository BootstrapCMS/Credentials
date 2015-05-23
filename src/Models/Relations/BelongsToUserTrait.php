<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@cachethq.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Models\Relations;

use Illuminate\Support\Facades\Config;

/**
 * This is the belongs to user trait.
 *
 * @author Graham Campbell <graham@cachethq.io>
 */
trait BelongsToUserTrait
{
    /**
     * Get the user relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Config::get('sentry.users.model'));
    }
}
