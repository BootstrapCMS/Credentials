<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Repositories\Interfaces;

/**
 * This is the slug repository interface.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
interface SlugRepositoryInterface
{
    /**
     * Find an existing model by slug.
     *
     * @param string   $slug
     * @param string[] $columns
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find($slug, array $columns = ['*']);
}
