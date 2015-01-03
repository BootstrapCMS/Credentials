<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Repositories;

/**
 * This is the user repository class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class UserRepository extends AbstractRepository
{
    use PaginateRepositoryTrait;
}
