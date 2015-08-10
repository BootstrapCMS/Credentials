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

use GrahamCampbell\Credentials\Facades\GroupRepository as GroupRepositoryFacade;
use GrahamCampbell\Credentials\Repositories\GroupRepository;
use GrahamCampbell\TestBenchCore\FacadeTrait;
use GrahamCampbell\Tests\Credentials\AbstractTestCase;

/**
 * This is the group repository facade test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class GroupRepositoryTest extends AbstractTestCase
{
    use FacadeTrait;

    /**
     * Get the facade accessor.
     *
     * @return string
     */
    protected function getFacadeAccessor()
    {
        return 'grouprepository';
    }

    /**
     * Get the facade class.
     *
     * @return string
     */
    protected function getFacadeClass()
    {
        return GroupRepositoryFacade::class;
    }

    /**
     * Get the facade route.
     *
     * @return string
     */
    protected function getFacadeRoot()
    {
        return GroupRepository::class;
    }
}
