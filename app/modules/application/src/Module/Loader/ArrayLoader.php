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
    public function load($name, array $config)
    {
        if (isset($this->values[$name])) {
            $config = array_replace_recursive($config, ['config' => $this->values[$name]]);
        }

        return $config;
    }
}
