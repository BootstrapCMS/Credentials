<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Carbon\Carbon;

if (!function_exists('html_ago')) {
    function html_ago(Carbon $carbon, $id = null)
    {
        if ($id) {
            return '<abbr id="'.$id.'" class="timeago" title="'.$carbon->toISO8601String().'">'.$carbon->toDateTimeString().'</abbr>';
        }

        return '<abbr class="timeago" title="'.$carbon->toISO8601String().'">'.$carbon->toDateTimeString().'</abbr>';
    }
}
