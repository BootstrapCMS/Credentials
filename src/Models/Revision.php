<?php

/*
 * This file is part of Laravel Credentials by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at http://bit.ly/UWsjkb.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace GrahamCampbell\Credentials\Models;

use GrahamCampbell\Credentials\Models\Relations\Common\BelongsToUserTrait;
use GrahamCampbell\Credentials\Models\Relations\Interfaces\BelongsToUserInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * This is the revision model class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md> Apache 2.0
 */
class Revision extends AbstractModel implements BelongsToUserInterface, HasPresenter
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
