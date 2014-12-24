<?php

/*
 * This file is part of Laravel Credentials by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at http://bit.ly/UWsjkb.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace GrahamCampbell\Tests\Credentials\Functional;

use GrahamCampbell\Tests\Credentials\AbstractTestCase;
use Illuminate\Contracts\Foundation\Application;

/**
 * This is the command test class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md> Apache 2.0
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
