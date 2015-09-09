<?php

namespace Pagekit\Module\Loader;

class ConfigLoader implements LoaderInterface
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
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * {@inheritdoc}
     */
    public function load($module)
    {
        if (isset($this->values[$module['name']])) {
            $module = array_replace_recursive($module, [
                'config' => $this->values[$module['name']]
            ]);
        }

        return $module;
    }
}
