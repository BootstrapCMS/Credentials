<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Subscribers;

use Illuminate\Console\Command;
use Illuminate\Events\Dispatcher;

/**
 * This is the command subscriber class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class CommandSubscriber
{
    /**
     * The forced flag.
     *
     * @var bool
     */
    protected $force;

    /**
     * Create a new instance.
     *
     * @param bool $force
     *
     * @return void
     */
    public function __construct($force)
    {
        $this->force = $force;
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     *
     * @return void
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            'command.runmigrations',
            'GrahamCampbell\Credentials\Subscribers\CommandSubscriber@onRunMigrations',
            8
        );
    }

    /**
     * Handle a command.runmigrations event.
     *
     * @param \Illuminate\Console\Command $command
     *
     * @return void
     */
    public function onRunMigrations(Command $command)
    {
        if ($this->force) {
            $command->call('migrate', ['--package' => 'cartalyst/sentry', '--force' => true]);
            $command->call('migrate', ['--package' => 'graham-campbell/credentials', '--force' => true]);
        } else {
            $command->call('migrate', ['--package' => 'cartalyst/sentry']);
            $command->call('migrate', ['--package' => 'graham-campbell/credentials']);
        }
    }
}
