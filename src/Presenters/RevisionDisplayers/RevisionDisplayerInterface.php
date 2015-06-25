<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Presenters\RevisionDisplayers;

/**
 * This is the revisional interface.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
interface RevisionDisplayerInterface
{
    /**
     * Get the change title.
     *
     * @return string
     */
    public function title();

    /**
     * Get the change description.
     *
     * @return string
     */
    public function description();
}
