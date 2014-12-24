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

namespace GrahamCampbell\Credentials\Presenters;

use McCool\LaravelAutoPresenter\BasePresenter;
use McCool\LaravelAutoPresenter\PresenterDecorator;

/**
 * This is the user presenter class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md> Apache 2.0
 */
class UserPresenter extends BasePresenter
{
    /**
     * The auto presenter instance.
     *
     * @var \McCool\LaravelAutoPresenter\PresenterDecorator
     */
    protected $presenter;

    /**
     * Create a new instance.
     *
     * @param \McCool\LaravelAutoPresenter\PresenterDecorator $presenter
     * @param \GrahamCampbell\Credentials\Models\User         $resource
     *
     * @return void
     */
    public function __construct(PresenterDecorator $presenter, $resource)
    {
        $this->presenter = $presenter;

        parent::__construct($resource);
    }

    /**
     * Get the user's name.
     *
     * @return string
     */
    public function name()
    {
        return $this->wrappedObject->first_name.' '.$this->wrappedObject->last_name;
    }

    /**
     * Get the user's security history.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function securityHistory()
    {
        $history = $this->wrappedObject->security()->get();

        $history->each(function ($item) {
            $item->security = true;
        });

        return $this->presenter->decorate($history);
    }

    /**
     * Get the user's action history.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function actionHistory()
    {
        $history = $this->wrappedObject->actions()->get();

        return $this->presenter->decorate($history);
    }

    /**
     * Get the auto presenter instance.
     *
     * @return \McCool\LaravelAutoPresenter\PresenterDecorator
     */
    public function getPresenter()
    {
        return $this->presenter;
    }
}
