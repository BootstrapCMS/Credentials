<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Models\Relations;

use DateTime;
use GrahamCampbell\Credentials\Facades\Credentials;
use GrahamCampbell\Credentials\Facades\RevisionRepository;
use Illuminate\Support\Facades\Config;

/**
 * This is the revisionable trait.
 *
 * This code was originally based on Chris Duell's Revisionable.
 * That code is licensed under the MIT License.
 * See the original here: http://bit.ly/1tZfndq.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
trait RevisionableTrait
{
    /**
     * Keeps track of the original data.
     *
     * @var array
     */
    protected $originalData;

    /**
     * Keeps track of the updated data.
     *
     * @var array
     */
    protected $updatedData;

    /**
     * Are we updating an existing model?
     *
     * @var bool
     */
    protected $updating;

    /**
     * Keeps track of columns to keep.
     *
     * @var array
     */
    protected $doKeep = [];

    /**
     * Keeps track of columns not to keep.
     *
     * @var array
     */
    protected $dontKeep = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Keeps the list of values that have been updated.
     *
     * @var array
     */
    protected $dirtyData = [];

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
        return $this->morphMany(Config::get('credentials.revision'), 'revisionable');
    }

    /**
     * Do some work before we start the saving process.
     *
     * @return void
     */
    public function preSave()
    {
        $this->originalData = $this->original;
        $this->updatedData = $this->attributes;

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
        if ($this->updating) {
            foreach ($this->changedRevisionableFields() as $key => $change) {
                RevisionRepository::create([
                    'revisionable_type' => get_class($this),
                    'revisionable_id'   => $this->getKey(),
                    'key'               => $key,
                    'old_value'         => $this->getDataValue('original', $key),
                    'new_value'         => $this->getDataValue('updated', $key),
                    'user_id'           => $this->getUserId(),
                ]);
            }
        } else {
            RevisionRepository::create([
                'revisionable_type' => get_class($this),
                'revisionable_id'   => $this->getKey(),
                'key'               => 'created_at',
                'old_value'         => null,
                'new_value'         => new DateTime(),
                'user_id'           => $this->getUserId(),
            ]);
        }
    }

    /**
     * Get the value to be saved, stripping passwords.
     *
     * @param string $type
     * @param string $key
     *
     * @return string|\DateTime
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
        RevisionRepository::create([
            'revisionable_type' => get_class($this),
            'revisionable_id'   => $this->getKey(),
            'key'               => 'deleted_at',
            'old_value'         => null,
            'new_value'         => new DateTime(),
            'user_id'           => $this->getUserId(),
        ]);
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
        } elseif (isset($this['user_id']) && $this['user_id']) {
            return $this['user_id'];
        }
    }

    /**
     * Get the fields for all of the storable changes that have been made.
     *
     * @return string[]
     */
    protected function changedRevisionableFields()
    {
        $changes = [];
        foreach ($this->dirtyData as $key => $value) {
            // check that the field is revisionable, and the data is dirty enough
            if ($this->isRevisionable($key) && !is_array($value)) {
                if (is_object($original = array_get($this->originalData, $key)) || is_string($original)) {
                    $original = trim($original);
                }

                if (is_object($updated = array_get($this->updatedData, $key)) || is_string($updated)) {
                    $updated = trim($updated);
                }

                if ($original != $updated) {
                    $changes[$key] = $value;
                }
            } else {
                // if it's not dirty enough, then remove the field from the array
                unset($this->updatedData[$key]);
                unset($this->originalData[$key]);
            }
        }

        return $changes;
    }

    /**
     * Check if this field should have a revision kept.
     *
     * If we are not tracking updates that null the field, and the update nulls
     * the field, then return false. If the field is explicitly revisionable,
     * then return true.  If it's explicitly not revisionable, return false.
     * Otherwise, if neither condition is met, only return true if  we aren't
     * specifying revisionable fields.
     *
     * @param string $key
     *
     * @return bool
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
