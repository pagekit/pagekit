<?php

namespace Pagekit\Module\Loader;

class ArrayLoader implements LoaderInterface
{
    /**
     * @var array
     */
    protected $values = [];

    /**
     * Constructor.
     *
     * @param array $values
     */
    public function __construct(array $values) {
        $this->values = $values;
    }

    /**
     * {@inheritdoc}
     */
    public function load($name, $module)
    {
        if (isset($this->values[$name])) {
            $module = array_replace_recursive($module, ['config' => $this->values[$name]]);
        }

        return $module;
    }
}
