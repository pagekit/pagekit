<?php

namespace Pagekit\Module\Factory;

class CallableFactory implements FactoryInterface
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
    public function __construct(callable $callable) {
        $this->callable = $callable;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $module)
    {
        return call_user_func($this->callable, $module);
    }
}
