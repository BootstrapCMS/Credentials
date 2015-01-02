<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Presenters;

/**
 * This is the author presenter trait.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
trait AuthorPresenterTrait
{
    /**
     * Get the author's name.
     *
     * @return string
     */
    public function author()
    {
        $user = $this->resource->user()->withTrashed()
            ->cacheDriver('array')->rememberForever()
            ->first(['first_name', 'last_name']);

        if ($user) {
            return $user->first_name.' '.$user->last_name;
        }
    }
}
