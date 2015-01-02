<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Presenters;

use GrahamCampbell\Credentials\Models\User;
use Illuminate\Support\Facades\App;
use McCool\LaravelAutoPresenter\BasePresenter;

/**
 * This is the user presenter class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class UserPresenter extends BasePresenter
{
    /**
     * Create a new instance.
     *
     * @param \GrahamCampbell\Credentials\Models\User $user
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->resource = $user;
    }

    /**
     * Get the user's name.
     *
     * @return string
     */
    public function name()
    {
        return $this->resource->first_name.' '.$this->resource->last_name;
    }

    /**
     * Get the user's security history.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function securityHistory()
    {
        $presenter = App::make('McCool\LaravelAutoPresenter\PresenterDecorator');
        $history = $this->resource->security()->get();

        $history->each(function ($item) {
            $item->security = true;
        });

        return $presenter->decorate($history);
    }

    /**
     * Get the user's action history.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function actionHistory()
    {
        $presenter = App::make('McCool\LaravelAutoPresenter\PresenterDecorator');
        $history = $this->resource->actions()->get();

        return $presenter->decorate($history);
    }
}
