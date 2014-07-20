<?php

/**
 * This file is part of Laravel Credentials by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace GrahamCampbell\Tests\Credentials\Functional;

use GrahamCampbell\Tests\Credentials\AbstractTestCase;

/**
 * This is the command test class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
class CommandTest extends AbstractTestCase
{
    /**
     * Additional application environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
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
        $this->assertEquals(0, $this->app['artisan']->call('app:install'));
    }

    public function testReset()
    {
        $this->assertEquals(0, $this->app['artisan']->call('migrate', array('--force' => true)));
        $this->assertEquals(0, $this->app['artisan']->call('app:reset'));
    }

    public function testUpdate()
    {
        $this->assertEquals(0, $this->app['artisan']->call('app:update'));
    }

    public function testResetAfterInstall()
    {
        $this->assertEquals(0, $this->app['artisan']->call('app:install'));
        $this->assertEquals(0, $this->app['artisan']->call('app:reset'));
    }
}
