<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Presenters\RevisionDisplayers;

use GrahamCampbell\Credentials\Presenters\RevisionPresenter;

/**
 * This is the abstract revision displayer class.
 *
 * @author Graham Campbell <graham@mineuk.com>
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
