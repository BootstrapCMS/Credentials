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

use Carbon\Carbon;
use Cartalyst\Sentry\Groups\GroupInterface;
use Cartalyst\Sentry\Users\Eloquent\User as SentryUser;
use GrahamCampbell\Credentials\Facades\Credentials;
use GrahamCampbell\Credentials\Facades\RevisionRepository;
use GrahamCampbell\Credentials\Models\Common\BaseModelTrait;
use GrahamCampbell\Credentials\Models\Interfaces\BaseModelInterface;
use GrahamCampbell\Credentials\Models\Relations\Common\RevisionableTrait;
use GrahamCampbell\Credentials\Models\Relations\Interfaces\RevisionableInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * This is the user model class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md> Apache 2.0
 */
class User extends SentryUser implements BaseModelInterface, RevisionableInterface, HasPresenter
{
    use BaseModelTrait, RevisionableTrait, SoftDeletes;

    /**
     * The table the users are stored in.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The model name.
     *
     * @var string
     */
    public static $name = 'user';

    /**
     * The properties on the model that are dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The revisionable columns.
     *
     * @var array
     */
    protected $keepRevisionOf = ['email', 'password', 'activated', 'last_login', 'first_name', 'last_name'];

    /**
     * The columns to select when displaying an index.
     *
     * @var array
     */
    public static $index = ['id', 'email', 'first_name', 'last_name'];

    /**
     * The max users per page when displaying a paginated index.
     *
     * @var int
     */
    public static $paginate = 20;

    /**
     * The columns to order by when displaying an index.
     *
     * @var string
     */
    public static $order = 'email';

    /**
     * The direction to order by when displaying an index.
     *
     * @var string
     */
    public static $sort = 'asc';

    /**
     * The user validation rules.
     *
     * @var array
     */
    public static $rules = [
        'first_name'            => 'required|min:2|max:32',
        'last_name'             => 'required|min:2|max:32',
        'email'                 => 'required|min:4|max:32|email',
        'password'              => 'required|min:6|confirmed',
        'password_confirmation' => 'required',
        'activated'             => 'required',
        'activated_at'          => 'required',
    ];

    /**
     * Access caches.
     *
     * @var array
     */
    protected $access = [];

    /**
     * Get the recent action history for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function revisions()
    {
        return $this->hasMany('GrahamCampbell\Credentials\Models\Revision');
    }

    /**
     * Get the user's action history.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function actions()
    {
        return $this->revisions()
            ->where(function ($q) {
                $q->where('revisionable_type', '<>', get_class($this))
                    ->where('user_id', '=', $this->id);
            })
            ->orWhere(function ($q) {
                $q->where('revisionable_type', '=', get_class($this))
                    ->where('revisionable_id', '<>', $this->id)
                    ->where('user_id', '=', $this->id);
            })
            ->orderBy('id', 'desc')->take(20);
    }

    /**
     * Get the user's security history.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function security()
    {
        return $this->revisionHistory()->orderBy('id', 'desc')->take(20);
    }

    /**
     * Get the presenter class.
     *
     * @return string
     */
    public function getPresenterClass()
    {
        return 'GrahamCampbell\Credentials\Presenters\UserPresenter';
    }

    /**
     * Activated at accessor.
     *
     * @param string $value
     *
     * @return \Carbon\Carbon|false
     */
    public function getActivatedAtAccessor($value)
    {
        if ($value) {
            return new Carbon($value);
        }

        if ($this->getAttribute('activated')) {
            return $this->getAttribute('created_at');
        }

        return false;
    }

    /**
     * Check a user's access.
     *
     * @param string|string[] $permissions
     * @param bool            $all
     *
     * @return bool
     */
    public function hasAccess($permissions, $all = true)
    {
        $key = sha1(json_encode($permissions).json_encode($all));

        if (!array_key_exists($key, $this->access)) {
            $this->access[$key] = parent::hasAccess($permissions, $all);
        }

        return $this->access[$key];
    }

    /**
     * Adds the user to the given group.
     *
     * @param \Cartalyst\Sentry\Groups\GroupInterface $group
     *
     * @return bool
     */
    public function addGroup(GroupInterface $group)
    {
        if (Credentials::check()) {
            RevisionRepository::create([
                'revisionable_type' => get_class($this),
                'revisionable_id'   => $this->getKey(),
                'key'               => 'added_group',
                'old_value'         => null,
                'new_value'         => $group->getName(),
                'user_id'           => Credentials::getUser()->id,
            ]);
        }

        return parent::addGroup($group);
    }

    /**
     * Removes the user from the given group.
     *
     * @param \Cartalyst\Sentry\Groups\GroupInterface $group
     *
     * @return bool
     */
    public function removeGroup(GroupInterface $group)
    {
        RevisionRepository::create([
            'revisionable_type' => get_class($this),
            'revisionable_id'   => $this->getKey(),
            'key'               => 'removed_group',
            'old_value'         => null,
            'new_value'         => $group->getName(),
            'user_id'           => Credentials::getUser()->id,
        ]);

        return parent::removeGroup($group);
    }
}
