<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\Credentials;

use GrahamCampbell\Credentials\Credentials;
use GrahamCampbell\Credentials\Repositories\GroupRepository;
use GrahamCampbell\Credentials\Repositories\RevisionRepository;
use GrahamCampbell\Credentials\Repositories\UserRepository;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;

/**
 * This is the service provider test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    public function testRevisionRepositoryIsInjectable()
    {
        $this->assertIsInjectable(RevisionRepository::class);
    }

    public function testUserRepositoryIsInjectable()
    {
        $this->assertIsInjectable(UserRepository::class);
    }

    public function testGroupRepositoryIsInjectable()
    {
        $this->assertIsInjectable(GroupRepository::class);
    }

    public function testCredentialsIsInjectable()
    {
        $this->assertIsInjectable(Credentials::class);
    }
}
