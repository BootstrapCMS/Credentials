<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Models\Interfaces;

use Illuminate\Database\Eloquent\Model;

/**
 * This is the base model interface.
 *
 * @author Graham Campbell <graham@mineuk.com>
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
