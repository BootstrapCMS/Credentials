<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Models\Relations\Interfaces;

/**
 * This is the revisionable interface.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
interface RevisionableInterface
{
    /**
     * Get the revision history relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function revisionHistory();
}
