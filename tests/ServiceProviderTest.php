<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\Credentials;

use GrahamCampbell\TestBench\Traits\ServiceProviderTestCaseTrait;

/**
 * This is the service provider test class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTestCaseTrait;

    public function testDifferIsInjectable()
    {
        $this->assertIsInjectable('SebastianBergmann\Diff\Differ');
    }

    public function testRevisionProviderIsInjectable()
    {
        $this->assertIsInjectable('GrahamCampbell\Credentials\Providers\RevisionProvider');
    }

    public function testUserProviderIsInjectable()
    {
        $this->assertIsInjectable('GrahamCampbell\Credentials\Providers\UserProvider');
    }

    public function testGroupProviderIsInjectable()
    {
        $this->assertIsInjectable('GrahamCampbell\Credentials\Providers\GroupProvider');
    }

    public function testCredentialsIsInjectable()
    {
        $this->assertIsInjectable('GrahamCampbell\Credentials\Credentials');
    }
}
