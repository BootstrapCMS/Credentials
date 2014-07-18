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

use Illuminate\Support\Facades\DB;
use GrahamCampbell\Credentials\Models\Revision;
use GrahamCampbell\Credentials\Facades\Credentials;

/**
 * This is the revisionable trait.
 *
 * Some code in this trait it taken from Chris Duell's Revisionable.
 * That code is licensed under the MIT License.
 * See the original here: http://bit.ly/1tZfndq.
 *
 * @package    Laravel-Credentials
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Credentials
 */
trait RevisionableTrait
{
    private $originalData;
    private $updatedData;
    private $updating;
    private $dontKeep = array();
    private $doKeep = array();

    /**
     * Keeps the list of values that have been updated
     * @var array
     */
    protected $dirtyData = array();

    /**
     * Create the event listeners for the saving and saved events
     * This lets us save revisions whenever a save is made, no matter the
     * http method.
     *
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

    public function revisionHistory()
    {
        return $this->morphMany('\GrahamCampbell\Credentials\Models\Revision', 'revisionable');
    }

    /**
     * Invoked before a model is saved. Return false to abort the operation.
     *
     * @return bool
     */
    public function preSave()
    {
        $this->originalData = $this->original;
        $this->updatedData  = $this->attributes;

        // we can only safely compare basic items,
        // so for now we drop any object based items, like DateTime
        foreach ($this->updatedData as $key => $val) {
            if (gettype($val) == 'object') {
                unset($this->originalData[$key]);
                unset($this->updatedData[$key]);
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
     * @return void
     */
    public function postSave()
    {
        if ($this->updating) {
            $changes_to_record = $this->changedRevisionableFields();
            $revisions = array();

            foreach ($changes_to_record as $key => $change) {

                $revisions[] = array(
                    'revisionable_type' => get_class($this),
                    'revisionable_id'   => $this->getKey(),
                    'key'               => $key,
                    'old_value'         => $this->getDataValue('original', $key),
                    'new_value'         => $this->getDataValue('updated', $key),
                    'user_id'           => $this->getUserId(),
                    'created_at'        => new \DateTime(),
                    'updated_at'        => new \DateTime(),
                );

            }

            if (count($revisions) > 0) {
                $revision = new Revision;
                DB::table($revision->getTable())->insert($revisions);
            }
        }
    }

    protected function getDataValue($type, $key)
    {
        if ($key == 'password') {
            return;
        }

        $name = $type.'Data';

        return array_get($this->$name, $key);
    }

    /**
     * If softdeletes are enabled, store the deleted time
     */
    public function postDelete()
    {
        if ($this->softDelete && $this->isRevisionable('deleted_at')) {
            $revisions[] = array(
                'revisionable_type' => get_class($this),
                'revisionable_id'   => $this->getKey(),
                'key'               => 'deleted_at',
                'old_value'         => null,
                'new_value'         => $this->deleted_at,
                'user_id'           => $this->getUserId(),
                'created_at'        => new \DateTime(),
                'updated_at'        => new \DateTime(),
            );
            $revision = new Revision;
            DB::table($revision->getTable())->insert($revisions);
        }
    }

    /**
     * Attempt to find the user id of the currently logged in user.
     **/
    private function getUserId()
    {
        if (Credentials::check()) {
            return Credentials::getUser()->id;
        }
    }

    /**
     * Get all of the changes that have been made, that are also supposed
     * to have their changes recorded
     * @return array fields with new data, that should be recorded
     */
    private function changedRevisionableFields()
    {
        $changes_to_record = array();
        foreach ($this->dirtyData as $key => $value) {
            // check that the field is revisionable, and double check
            // that it's actually new data in case dirty is, well, clean
            if ($this->isRevisionable($key) && !is_array($value)) {
                if (!isset($this->originalData[$key]) || $this->originalData[$key] != $this->updatedData[$key]) {
                    $changes_to_record[$key] = $value;
                }
            } else {
                // we don't need these any more, and they could
                // contain a lot of data, so lets trash them.
                unset($this->updatedData[$key]);
                unset($this->originalData[$key]);
            }
        }

        return $changes_to_record;
    }

    /**
     * Check if this field should have a revision kept
     *
     * @param string $key
     *
     * @return boolean
     */
    private function isRevisionable($key)
    {
        // If the field is explicitly revisionable, then return true.
        // If it's explicitly not revisionable, return false.
        // Otherwise, if neither condition is met, only return true if
        // we aren't specifying revisionable fields.
        if (isset($this->doKeep) && in_array($key, $this->doKeep)) return true;
        if (isset($this->dontKeep) && in_array($key, $this->dontKeep)) return false;
        return empty($this->doKeep);
    }

    /**
     * Identifiable Name
     * When displaying revision history, when a foreigh key is updated
     * instead of displaying the ID, you can choose to display a string
     * of your choice, just override this method in your model
     * By default, it will fall back to the models ID.
     * @return string an identifying name for the model
     */
    public function identifiableName()
    {
        return $this->getKey();
    }

    /**
     * Revision Unknown String
     * When displaying revision history, when a foreigh key is updated
     * instead of displaying the ID, you can choose to display a string
     * of your choice, just override this method in your model
     * By default, it will fall back to the models ID.
     * @return string an identifying name for the model
     */
    public function getRevisionNullString()
    {
        return isset($this->revisionNullString)?$this->revisionNullString:'nothing';
    }

    /**
     * No revision string
     * When displaying revision history, if the revisions value
     * cant be figured out, this is used instead.
     * It can be overridden.
     * @return string an identifying name for the model
     */
    public function getRevisionUnknownString()
    {
        return isset($this->revisionUnknownString)?$this->revisionUnknownString:'unknown';
    }
}
