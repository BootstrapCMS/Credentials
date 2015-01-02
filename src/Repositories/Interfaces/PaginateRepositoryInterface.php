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
 * This is the paginate repository interface.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
interface PaginateRepositoryInterface
{
    /**
     * Get a paginated list of the models.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function paginate();

    /**
     * Get the paginated links.
     *
     * @return string
     */
    public function links();
}
