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

use Exception;
use GrahamCampbell\Credentials\Facades\Credentials;
use GrahamCampbell\Credentials\Facades\Differ;
use GrahamCampbell\Credentials\Models\Revision;
use McCool\LaravelAutoPresenter\BasePresenter;

/**
 * This is the revision presenter class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class RevisionPresenter extends BasePresenter
{
    use AuthorPresenterTrait;

    /**
     * Create a new instance.
     *
     * @param \GrahamCampbell\Credentials\Models\Revision $revision
     *
     * @return void
     */
    public function __construct(Revision $revision)
    {
        $this->resource = $revision;
    }

    /**
     * Get the change title.
     *
     * @return string
     */
    public function title()
    {
        $class = $this->getDisplayerClass();

        return with(new $class($this))->title();
    }

    /**
     * Get the change description.
     *
     * @return string
     */
    public function description()
    {
        $class = $this->getDisplayerClass();

        return with(new $class($this))->description();
    }

    /**
     * Get the relevant displayer class.
     *
     * @throws \Exception
     *
     * @return string
     */
    protected function getDisplayerClass()
    {
        $class = $this->resource->revisionable_type;

        do {
            if (class_exists($displayer = $this->generateDisplayerName($class))) {
                return $displayer;
            }
        } while ($class = get_parent_class($class));

        throw new Exception('No displayers could be found');
    }

    /**
     * Generate a possible displayer class name.
     *
     * @return string
     */
    protected function generateDisplayerName($class)
    {
        $shortArray = explode('\\', $class);
        $short = end($shortArray);
        $field = studly_case($this->field());

        $temp = str_replace($short, 'RevisionDisplayers\\'.$short.'\\'.$field.'Displayer', $class);

        return str_replace('Model', 'Presenter', $temp);
    }

    /**
     * Get the change field.
     *
     * @return string
     */
    public function field()
    {
        if (strpos($this->resource->key, '_id')) {
            return str_replace('_id', '', $this->resource->key);
        }

        return $this->resource->key;
    }

    /**
     * Get diff.
     *
     * @return string
     */
    public function diff()
    {
        return Differ::diff($this->resource->old_value, $this->resource->new_value);
    }

    /**
     * Was the event invoked by the current user?
     *
     * @return bool
     */
    public function wasByCurrentUser()
    {
        return (Credentials::check() && Credentials::getUser()->id == $this->resource->user_id);
    }
}
