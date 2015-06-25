<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * This is the abstract model class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
abstract class AbstractModel extends Eloquent
{
    use BaseModelTrait;

    /**
     * A list of methods protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = ['_token', '_method', 'id'];
}
