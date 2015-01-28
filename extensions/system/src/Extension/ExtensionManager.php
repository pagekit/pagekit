<?php

namespace Pagekit\Extension;

use Pagekit\Application as App;
use Pagekit\System\Package\PackageManager;

class ExtensionManager extends PackageManager
{
    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return App::module()->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function load($name, $path = null)
    {
    }

    /**
     * Implements the \IteratorAggregate.
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator(App::module()->all());
    }
}
