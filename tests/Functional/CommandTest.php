<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\Credentials\Functional;

use GrahamCampbell\Tests\Credentials\AbstractTestCase;
use Illuminate\Contracts\Foundation\Application;

/**
 * This is the command test class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class CommandTest extends AbstractTestCase
{
    /**
     * Additional application environment setup.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    protected function additionalSetup($app)
    {
        if (!class_exists('DatabaseSeeder')) {
            eval('class DatabaseSeeder extends Illuminate\Database\Seeder { public function run() {} }');
        }
    }

    public function testInstall()
    {
        $this->assertSame(0, $this->getKernel()->call('app:install'));
    }

    public function testReset()
    {
        $this->assertSame(0, $this->getKernel()->call('migrate', ['--force' => true]));
        $this->assertSame(0, $this->getKernel()->call('app:reset'));
    }

    public function testUpdate()
    {
        $this->assertSame(0, $this->getKernel()->call('app:update'));
    }

    public function testResetAfterInstall()
    {
        $this->assertSame(0, $this->getKernel()->call('app:install'));
        $this->assertSame(0, $this->getKernel()->call('app:reset'));
    }

    protected function getKernel()
    {
        return $this->app->make('Illuminate\Contracts\Console\Kernel');
    }
}
