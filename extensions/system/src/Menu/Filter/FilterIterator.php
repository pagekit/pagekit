<?php

namespace Pagekit\Menu\Filter;

use Pagekit\Framework\Application;

abstract class FilterIterator extends \FilterIterator
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var Application
     */
    protected static $app;

    /**
     * Constructor.
     *
     * @param \Iterator $iterator
     * @param array     $options
     */
    public function __construct(\Iterator $iterator, array $options = array())
    {
        parent::__construct($iterator);

        $this->options = $options;
    }

    /**
     * Gets the application.
     *
     * @return Application
     */
    public static function getApplication()
    {
        return self::$app;
    }

    /**
     * Sets the application.
     *
     * @param Application $app
     */
    public static function setApplication(Application $app)
    {
        self::$app = $app;
    }

    /**
     * Gets an application parameter or an object.
     *
     * @param  string $id
     * @return mixed
     */
    public function __invoke($id)
    {
        return self::$app[$id];
    }
}
