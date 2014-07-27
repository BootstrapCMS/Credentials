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

namespace GrahamCampbell\Credentials\Models\Relations\Common;

use DateTime;
use GrahamCampbell\Credentials\Facades\Credentials;
use GrahamCampbell\Credentials\Models\Revision;
use Illuminate\Support\Facades\DB;

/**
 * This is the revisionable trait.
 *
 * This code was originally based on Chris Duell's Revisionable.
 * That code is licensed under the MIT License.
 * See the original here: http://bit.ly/1tZfndq.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md> Apache 2.0
 */
trait RevisionableTrait
{
    /**
     * Keeps track of the original data.
     *
     * @type array
     */
    protected $originalData;

    /**
     * Keeps track of the updated data.
     *
     * @type array
     */
    protected $updatedData;

    /**
     * Are we updating an existing model?
     *
     * @type bool
     */
    protected $updating;

    /**
     * Keeps track of columns to keep.
     *
     * @type array
     */
    protected $doKeep = array();

    /**
     * Keeps track of columns not to keep.
     *
     * @type array
     */
    protected $dontKeep = array('id', 'created_at', 'updated_at', 'deleted_at');

    /**
     * Keeps the list of values that have been updated.
     *
     * @type array
     */
    protected $dirtyData = array();

    /**
     * Create the event listeners for the saving and saved events.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->preSave();
        });

        static::saved(function ($model) {
            $model->postSave();
        });

        static::deleted(function ($model) {
            $model->preSave();
            $model->postDelete();
        });
    }

    /**
     * Get the revision history relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function revisionHistory()
    {
        return $this->morphMany('GrahamCampbell\Credentials\Models\Revision', 'revisionable');
    }

    /**
     * Get the identifiable name.
     *
     * When displaying revision history, when a foreign key is updated
     * instead of displaying the ID, you can choose to display a string
     * of your choice, just override this method in your model.
     * By default, it will fall back to the models ID.
     *
     * @return string
     */
    public function identifiableName()
    {
        return $this->getKey();
    }

    /**
     * Do some work before we start the saving process.
     *
     * @return void
     */
    public function preSave()
    {
        $this->originalData = $this->original;
        $this->updatedData  = $this->attributes;

        // we can only safely compare basic items, so for now we drop any object based
        // items apart from DateTime objects where we compare them specially
        foreach ($this->updatedData as $key => $val) {
            if (is_object($val)) {
                if (!($val instanceof DateTime)) {
                    unset($this->originalData[$key]);
                    unset($this->updatedData[$key]);
                }
            }
        }

        // the below is ugly, for sure, but it's required so we can save the standard model
        // then use the keep / dontkeep values for later, in the isRevisionable method
        $this->dontKeep = isset($this->dontKeepRevisionOf) ?
            $this->dontKeepRevisionOf + $this->dontKeep
            : $this->dontKeep;

        $this->doKeep = isset($this->keepRevisionOf) ?
            $this->keepRevisionOf + $this->doKeep
            : $this->doKeep;

        unset($this->attributes['dontKeepRevisionOf']);
        unset($this->attributes['keepRevisionOf']);

        $this->dirtyData = $this->getDirty();
        $this->updating = $this->exists;
    }

    /**
     * Called after a model is successfully saved.
     *
     * If the model is new, we log it's time of creation.
     * If the model was updated, then we log each updated field separately.
     *
     * @return void
     */
    public function postSave()
    {
        $revisions = array();

        if ($this->updating) {
            $changes = $this->changedRevisionableFields();

            foreach ($changes as $key => $change) {
                $revisions[] = array(
                    'revisionable_type' => get_class($this),
                    'revisionable_id'   => $this->getKey(),
                    'key'               => $key,
                    'old_value'         => $this->getDataValue('original', $key),
                    'new_value'         => $this->getDataValue('updated', $key),
                    'user_id'           => $this->getUserId(),
                    'created_at'        => new DateTime(),
                    'updated_at'        => new DateTime(),
                );
            }

            if (count($revisions) > 0) {
                $revision = new Revision;
                DB::table($revision->getTable())->insert($revisions);
            }
        } else {
            $revisions[] = array(
                'revisionable_type' => get_class($this),
                'revisionable_id'   => $this->getKey(),
                'key'               => 'created_at',
                'old_value'         => null,
                'new_value'         => new DateTime(),
                'user_id'           => $this->getUserId(),
                'created_at'        => new DateTime(),
                'updated_at'        => new DateTime(),
            );

            $revision = new Revision;
            DB::table($revision->getTable())->insert($revisions);
        }
    }

    /**
     * Get the value to be saved, stripping passwords.
     *
     * @param string $type
     * @param string $key
     *
     * @return string|\Carbon\Carbon
     */
    protected function getDataValue($type, $key)
    {
        if ($key == 'password') {
            return;
        }

        $name = $type.'Data';

        return array_get($this->$name, $key);
    }

    /**
     * Store the deleted time.
     *
     * @return void
     */
    public function postDelete()
    {
        $revisions = array();

        $revisions[] = array(
            'revisionable_type' => get_class($this),
            'revisionable_id'   => $this->getKey(),
            'key'               => 'deleted_at',
            'old_value'         => null,
            'new_value'         => new DateTime(),
            'user_id'           => $this->getUserId(),
            'created_at'        => new DateTime(),
            'updated_at'        => new DateTime(),
        );

        $revision = new Revision;
        DB::table($revision->getTable())->insert($revisions);
    }

    /**
     * Attempt to find the user id of the currently logged in user.
     *
     * @return int|null
     */
    protected function getUserId()
    {
        if (Credentials::check()) {
            return Credentials::getUser()->id;
        }
    }

    /**
     * Get the fields for all of the storable changes that have been made.
     *
     * @return string[]
     */
    protected function changedRevisionableFields()
    {
        $changes = array();
        foreach ($this->dirtyData as $key => $value) {
            // check that the field is revisionable, and the data is dirty enough
            if ($this->isRevisionable($key) && !is_array($value)) {
                if (is_object($original = array_get($this->originalData, $key))) {
                    $original = $original->getTimestamp();
                } elseif (is_string($original)) {
                    $original = trim($original);
                }

                if (is_object($updated = array_get($this->updatedData, $key))) {
                    $updated = $updated->getTimestamp();
                } elseif (is_string($updated)) {
                    $updated = trim($updated);
                }

                if ($original != $updated) {
                    $changes[$key] = $value;
                }
            } else {
                // we're done with each key, each time, so let's save memory
                unset($this->updatedData[$key]);
                unset($this->originalData[$key]);
            }
        }

        return $changes;
    }

    /**
     * Check if this field should have a revision kept.
     *
     * If the field is explicitly revisionable, then return true.
     * If it's explicitly not revisionable, return false.
     * Otherwise, if neither condition is met, only return true if
     * we aren't specifying revisionable fields.
     *
     * @param string $key
     *
     * @return boolean
     */
    protected function isRevisionable($key)
    {
        if (isset($this->doKeep) && in_array($key, $this->doKeep)) {
            return true;
        }

        if (isset($this->dontKeep) && in_array($key, $this->dontKeep)) {
            return false;
        }

        return empty($this->doKeep);
    }
}
