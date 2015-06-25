<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Repositories;

/**
 * This is the base repository trait.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
trait BaseRepositoryTrait
{
    /**
     * Create a new model.
     *
     * @param array $input
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $input)
    {
        $model = $this->model;

        return $model::create($input);
    }

    /**
     * Find an existing model.
     *
     * @param int      $id
     * @param string[] $columns
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find($id, array $columns = ['*'])
    {
        $model = $this->model;

        return $model::find($id, $columns);
    }

    /**
     * Find all models.
     *
     * @param string[] $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $columns = ['*'])
    {
        $model = $this->model;

        return $model::all($columns);
    }

    /**
     * Get a list of the models.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        $model = $this->model;

        if (property_exists($model, 'order')) {
            return $model::orderBy($model::$order, $model::$sort)->get($model::$index);
        }

        return $model::get($model::$index);
    }

    /**
     * Get the number of rows.
     *
     * @return int
     */
    public function count()
    {
        $model = $this->model;

        return $model::where('id', '>=', 1)->count();
    }

    /**
     * Register an observer.
     *
     * @param object $observer
     *
     * @return $this
     */
    public function observe($observer)
    {
        $model = $this->model;
        $model::observe($observer);

        return $this;
    }

    /**
     * Return the rules.
     *
     * @param string|string[] $query
     *
     * @return string[]
     */
    public function rules($query = null)
    {
        $model = $this->model;

        // get rules from the model if set
        if (isset($model::$rules)) {
            $rules = $model::$rules;
        } else {
            $rules = [];
        }

        // if the there are no rules
        if (!is_array($rules) || !$rules) {
            // return an empty array
            return [];
        }

        // if the query is empty
        if (!$query) {
            // return all of the rules
            return array_filter($rules);
        }

        // return the relevant rules
        return array_filter(array_only($rules, $query));
    }

    /**
     * Validate the data.
     *
     * @param array           $data
     * @param string|string[] $rules
     * @param bool            $custom
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validate(array $data, $rules = null, $custom = false)
    {
        if (!$custom) {
            $rules = $this->rules($rules);
        }

        return $this->validator->make($data, $rules);
    }
}
