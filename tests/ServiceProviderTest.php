<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@cachethq.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\Credentials;

use GrahamCampbell\TestBench\Traits\ServiceProviderTestCaseTrait;

/**
 * This is the service provider test class.
 *
 * @author Graham Campbell <graham@cachethq.io>
 */
class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTestCaseTrait;

    public function testDifferIsInjectable()
    {
        $this->assertIsInjectable('SebastianBergmann\Diff\Differ');
    }

    public function testRevisionRepositoryIsInjectable()
    {
        $this->assertIsInjectable('GrahamCampbell\Credentials\Repositories\RevisionRepository');
    }

    public function testUserRepositoryIsInjectable()
    {
        $this->assertIsInjectable('GrahamCampbell\Credentials\Repositories\UserRepository');
    }

    public function testGroupRepositoryIsInjectable()
    {
        $this->assertIsInjectable('GrahamCampbell\Credentials\Repositories\GroupRepository');
    }

    public function testCredentialsIsInjectable()
    {
        $this->assertIsInjectable('GrahamCampbell\Credentials\Credentials');
    }
}
