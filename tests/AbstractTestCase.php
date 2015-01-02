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

use GrahamCampbell\TestBench\AbstractLaravelTestCase as TestCase;

/**
 * This is the abstract test case class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
abstract class AbstractTestCase extends TestCase
{
    /**
     * Get the required service providers.
     *
     * @return string[]
     */
    protected function getRequiredServiceProviders()
    {
        return [
            'McCool\LaravelAutoPresenter\LaravelAutoPresenterServiceProvider',
            'Cartalyst\Sentry\SentryServiceProvider',
            'GrahamCampbell\Core\CoreServiceProvider',
            'GrahamCampbell\Security\SecurityServiceProvider',
            'GrahamCampbell\Binput\BinputServiceProvider',
            'GrahamCampbell\Throttle\ThrottleServiceProvider',
        ];
    }

    /**
     * Get the service provider class.
     *
     * @return string
     */
    protected function getServiceProviderClass()
    {
        return 'GrahamCampbell\Credentials\CredentialsServiceProvider';
    }
}
