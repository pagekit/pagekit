<?php

namespace Pagekit\Menu\Filter;

use Pagekit\Component\Menu\Filter\FilterIterator as BaseFilterIterator;
use Pagekit\Framework\Application;

abstract class FilterIterator extends BaseFilterIterator
{
    /**
     * @var Application
     */
    protected static $app;

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
