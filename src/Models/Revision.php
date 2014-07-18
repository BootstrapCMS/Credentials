<?php

/**
 * This file is part of Laravel Credentials by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace GrahamCampbell\Credentials\Models;

use GrahamCampbell\Database\Models\AbstractModel;
use McCool\LaravelAutoPresenter\PresenterInterface;
use GrahamCampbell\Credentials\Models\Relations\Interfaces\BelongsToUserInterface;
use GrahamCampbell\Credentials\Models\Relations\Common\BelongsToUserTrait;

/**
 * This is the revision model class.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
class Revision extends AbstractModel implements BelongsToUserInterface, PresenterInterface
{
    use BelongsToUserTrait;

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
     * The columns to select when displaying an index.
     *
     * @var array
     */
    public static $index = array('*');

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

    public function revisionable()
    {
        return $this->morphTo();
    }

    /**
     * Get the presenter class.
     *
     * @var string
     */
    public function getPresenter()
    {
        return 'GrahamCampbell\Credentials\Presenters\RevisionPresenter';
    }
}
