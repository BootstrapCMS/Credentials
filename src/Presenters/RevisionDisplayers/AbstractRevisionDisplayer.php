<?php

/**
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

namespace GrahamCampbell\Credentials\Presenters\RevisionDisplayers;

use GrahamCampbell\Credentials\Presenters\RevisionPresenter;

/**
 * This is the abstract revision displayer class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md> Apache 2.0
 */
abstract class AbstractRevisionDisplayer
{
    /**
     * The presenter instance.
     *
     * @var \GrahamCampbell\Credentials\Presenters\RevisionPresenter
     */
    protected $presenter;

    /**
     * The resource instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $resource;

    /**
     * Create a new instance.
     *
     * @param \GrahamCampbell\Credentials\Presenters\RevisionPresenter $presenter
     *
     * @return void
     */
    public function __construct(RevisionPresenter $presenter)
    {
        $this->presenter = $presenter;
        $this->resource = $this->presenter->resource;
    }

    /**
     * Get the change details.
     *
     * @return string
     */
    protected function details()
    {
        return ' from "'.$this->resource->old_value.'" to "'.$this->resource->new_value.'".';
    }
}
