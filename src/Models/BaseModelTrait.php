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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event as LaravelEvent;

/**
 * This is the base model trait.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
trait BaseModelTrait
{
    /**
     * Create a new model.
     *
     * @param array $input
     *
     * @throws \Exception
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function create(array $input = [])
    {
        DB::beginTransaction();

        try {
            LaravelEvent::fire(static::$name.'.creating');
            static::beforeCreate($input);
            $return = parent::create($input);
            static::afterCreate($input, $return);
            LaravelEvent::fire(static::$name.'.created');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $return;
    }

    /**
     * Before creating a new model.
     *
     * @param array $input
     *
     * @return void
     */
    public static function beforeCreate(array $input)
    {
        // can be overwritten by extending class
    }

    /**
     * After creating a new model.
     *
     * @param array                               $input
     * @param \Illuminate\Database\Eloquent\Model $return
     *
     * @return void
     */
    public static function afterCreate(array $input, Model $return)
    {
        // can be overwritten by extending class
    }

    /**
     * Update an existing model.
     *
     * @param array $input
     *
     * @throws \Exception
     *
     * @return bool|int
     */
    public function update(array $input = [])
    {
        DB::beginTransaction();

        try {
            LaravelEvent::fire(static::$name.'.updating', $this);
            $this->beforeUpdate($input);
            $return = parent::update($input);
            $this->afterUpdate($input, $return);
            LaravelEvent::fire(static::$name.'.updated', $this);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $return;
    }

    /**
     * Before updating an existing new model.
     *
     * @param array $input
     *
     * @return void
     */
    public function beforeUpdate(array $input)
    {
        // can be overwritten by extending class
    }

    /**
     * After updating an existing model.
     *
     * @param array    $input
     * @param bool|int $return
     *
     * @return void
     */
    public function afterUpdate(array $input, $return)
    {
        // can be overwritten by extending class
    }

    /**
     * Delete an existing model.
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function delete()
    {
        DB::beginTransaction();

        try {
            LaravelEvent::fire(static::$name.'.deleting', $this);
            $this->beforeDelete();
            $return = parent::delete();
            $this->afterDelete($return);
            LaravelEvent::fire(static::$name.'.deleted', $this);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $return;
    }

    /**
     * Before deleting an existing model.
     *
     * @return void
     */
    public function beforeDelete()
    {
        // can be overwritten by extending class
    }

    /**
     * After deleting an existing model.
     *
     * @param bool $return
     *
     * @return void
     */
    public function afterDelete($return)
    {
        // can be overwritten by extending class
    }
}
