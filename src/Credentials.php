<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials;

use Cartalyst\Sentry\Sentry;
use McCool\LaravelAutoPresenter\PresenterDecorator;

/**
 * This is the credentials class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class Credentials
{
    /**
     * The cache of the check method.
     *
     * @var mixed
     */
    protected $cache;

    /**
     * The sentry instance.
     *
     * @var \Cartalyst\Sentry\Sentry
     */
    protected $sentry;

    /**
     * The decorator instance.
     *
     * @var \McCool\LaravelAutoPresenter\PresenterDecorator
     */
    protected $decorator;

    /**
     * Create a new instance.
     *
     * @param \Cartalyst\Sentry\Sentry                        $sentry
     * @param \McCool\LaravelAutoPresenter\PresenterDecorator $decorator
     *
     * @return void
     */
    public function __construct(Sentry $sentry, PresenterDecorator $decorator)
    {
        $this->sentry = $sentry;
        $this->decorator = $decorator;
    }

    /**
     * Call Sentry's check method or load of cached value.
     *
     * @return bool
     */
    public function check()
    {
        if ($this->cache === null) {
            $this->cache = $this->sentry->check();
        }

        return $this->cache && $this->getUser();
    }

    /**
     * Get the decorated current user.
     *
     * @return \GrahamCampbell\Credentials\Presenters\UserPresenter
     */
    public function getDecoratedUser()
    {
        if ($user = $this->sentry->getUser()) {
            return $this->decorator->decorate($user);
        }
    }

    /**
     * Dynamically pass all other methods to sentry.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->sentry, $method], $parameters);
    }
}
