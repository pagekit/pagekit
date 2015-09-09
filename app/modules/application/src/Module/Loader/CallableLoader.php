<?php

namespace Pagekit\Module\Loader;

class CallableLoader implements LoaderInterface
{
    /**
     * @var callable
     */
    protected $callable;

    /**
     * Constructor.
     *
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * {@inheritdoc}
     */
    public function load($module)
    {
        return call_user_func($this->callable, $module);
    }
}
