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

use GrahamCampbell\Credentials\Models\Relations\BelongsToUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * This is the revision model class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class Revision extends AbstractModel implements HasPresenter
{
    use BelongsToUserTrait, SoftDeletes;

    /**
     * The table the groups are stored in.
     *
     * @var string
     */
    protected $table = 'revisions';

    /**
     * The model name.
     *
     * @var string
     */
    public static $name = 'revision';

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
    public static $index = ['*'];

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
    public static $order = 'id';

    /**
     * This defines if the model should be treated
     * in the context of being a security action.
     *
     * @var bool
     */
    public $security = false;

    /**
     * Get the model the action as been taken on.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function revisionable()
    {
        return $this->morphTo();
    }

    /**
     * Get the presenter class.
     *
     * @return string
     */
    public function getPresenterClass()
    {
        return 'GrahamCampbell\Credentials\Presenters\RevisionPresenter';
    }
}
