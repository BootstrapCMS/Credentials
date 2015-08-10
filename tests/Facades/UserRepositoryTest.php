<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\Credentials\Facades;

use GrahamCampbell\Credentials\Facades\UserRepository as UserRepositoryFacade;
use GrahamCampbell\Credentials\Repositories\UserRepository;
use GrahamCampbell\TestBenchCore\FacadeTrait;
use GrahamCampbell\Tests\Credentials\AbstractTestCase;

/**
 * This is the user repository facade test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class UserRepositoryTest extends AbstractTestCase
{
    use FacadeTrait;

    /**
     * Get the facade accessor.
     *
     * @return string
     */
    protected function getFacadeAccessor()
    {
        return 'userrepository';
    }

    /**
     * Get the facade class.
     *
     * @return string
     */
    protected function getFacadeClass()
    {
        return UserRepositoryFacade::class;
    }

    /**
     * Get the facade route.
     *
     * @return string
     */
    protected function getFacadeRoot()
    {
        return UserRepository::class;
    }
}
