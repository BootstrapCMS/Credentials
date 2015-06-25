<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Presenters;

use McCool\LaravelAutoPresenter\BasePresenter;
use McCool\LaravelAutoPresenter\PresenterDecorator;

/**
 * This is the user presenter class.
 *
 * @author Graham Campbell <graham@alt-three.com>
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
