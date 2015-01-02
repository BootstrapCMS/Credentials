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
use GrahamCampbell\Credentials\Credentials;
use McCool\LaravelAutoPresenter\BasePresenter;
use SebastianBergmann\Diff\Differ;

/**
 * This is the revision presenter class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class RevisionPresenter extends BasePresenter
{
    use AuthorPresenterTrait;

    /**
     * The credentials instance.
     *
     * @var \GrahamCampbell\Credentials\Credentials
     */
    protected $credentials;

    /**
     * The differ instance.
     *
     * @var \SebastianBergmann\Diff\Differ
     */
    protected $differ;

    /**
     * Create a new instance.
     *
     * @param \GrahamCampbell\Credentials\Credentials     $credentials
     * @param \SebastianBergmann\Diff\Differ              $differ
     * @param \GrahamCampbell\Credentials\Models\Revision $resource
     *
     * @return void
     */
    public function __construct(Credentials $credentials, Differ $differ, $resource)
    {
        $this->credentials = $credentials;
        $this->differ = $differ;

        parent::__construct($resource);
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
        $class = $this->wrappedObject->revisionable_type;

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
        if (strpos($this->wrappedObject->key, '_id')) {
            return str_replace('_id', '', $this->wrappedObject->key);
        }

        return $this->wrappedObject->key;
    }

    /**
     * Get diff.
     *
     * @return string
     */
    public function diff()
    {
        return $this->differ->diff($this->wrappedObject->old_value, $this->wrappedObject->new_value);
    }

    /**
     * Was the event invoked by the current user?
     *
     * @return bool
     */
    public function wasByCurrentUser()
    {
        return ($this->credentials->check() && $this->credentials->getUser()->id == $this->wrappedObject->user_id);
    }

    /**
     * Get credentials instance.
     *
     * @return \GrahamCampbell\Credentials\Credentials
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * Get the differ instance.
     *
     * @return \SebastianBergmann\Diff\Differ
     */
    public function getDiffer()
    {
        return $this->differ;
    }
}
