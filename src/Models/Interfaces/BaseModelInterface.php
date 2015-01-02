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

namespace GrahamCampbell\Credentials\Models\Interfaces;

use Illuminate\Database\Eloquent\Model;

/**
 * This is the base model interface.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md> Apache 2.0
 */
interface BaseModelInterface
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
    public static function create(array $input);

    /**
     * Before creating a new model.
     *
     * @param array $input
     *
     * @return void
     */
    public static function beforeCreate(array $input);

    /**
     * After creating a new model.
     *
     * @param array                               $input
     * @param \Illuminate\Database\Eloquent\Model $return
     *
     * @return void
     */
    public static function afterCreate(array $input, Model $return);

    /**
     * Update an existing model.
     *
     * @param array $input
     *
     * @throws \Exception
     *
     * @return bool|int
     */
    public function update(array $input = []);

    /**
     * Before updating an existing new model.
     *
     * @param array $input
     *
     * @return void
     */
    public function beforeUpdate(array $input);

    /**
     * After updating an existing model.
     *
     * @param array    $input
     * @param bool|int $return
     *
     * @return void
     */
    public function afterUpdate(array $input, $return);

    /**
     * Delete an existing model.
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function delete();

    /**
     * Before deleting an existing model.
     *
     * @return void
     */
    public function beforeDelete();

    /**
     * After deleting an existing model.
     *
     * @param bool $return
     *
     * @return void
     */
    public function afterDelete($return);
}
