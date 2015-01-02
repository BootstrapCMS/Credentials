<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (Config::get('graham-campbell/core::commands')) {
    $subscriber = App::make('GrahamCampbell\Credentials\Subscribers\CommandSubscriber');
    Event::subscribe($subscriber);
}
