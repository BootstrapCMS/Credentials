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

use Cartalyst\Sentry\Groups\Eloquent\Group as SentryGroup;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the group model class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class Group extends SentryGroup
{
    use BaseModelTrait, SoftDeletes;

    /**
     * The table the groups are stored in.
     *
     * @var string
     */
    protected $table = 'groups';

    /**
     * The model name.
     *
     * @var string
     */
    public static $name = 'group';

    /**
     * The properties on the model that are dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The columns to select when displaying an index.
     *
     * @var array
     */
    public static $index = ['id', 'name'];

    /**
     * The max groups per page when displaying a paginated index.
     *
     * @var int
     */
    public static $paginate = 20;

    /**
     * The columns to order by when displaying an index.
     *
     * @var string
     */
    public static $order = 'name';

    /**
     * The direction to order by when displaying an index.
     *
     * @var string
     */
    public static $sort = 'asc';
}
