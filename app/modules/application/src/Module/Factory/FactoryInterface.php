<?php

namespace Pagekit\Module\Factory;

interface FactoryInterface
{
    /**
     * Creates the module.
     *
     * @param  array $module
     * @return mixed
     */
    public function create(array $module);
}
