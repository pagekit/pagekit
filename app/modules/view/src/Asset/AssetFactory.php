<?php

namespace Pagekit\View\Asset;

class AssetFactory
{
    /**
     * @var array
     */
    protected $types = [
        'file'   => 'Pagekit\View\Asset\FileAsset',
        'string' => 'Pagekit\View\Asset\StringAsset'
    ];

    /**
     * Create an asset instance.
     *
     * @param  string $name
     * @param  mixed  $source
     * @param  mixed  $dependencies
     * @param  mixed  $options
     * @return AssetInterface
     * @throws \InvalidArgumentException
     */
    public function create($name, $source, $dependencies = [], $options = [])
    {
        if (is_string($dependencies)) {
            $dependencies = [$dependencies];
        }

        if (is_string($options)) {
            $options = ['type' => $options];
        }

        if (!isset($options['type'])) {
            $options['type'] = 'file';
        }

        if (isset($this->types[$options['type']])) {

            $class = $this->types[$options['type']];

            return new $class($name, $source, $dependencies, $options);
        }

        throw new \InvalidArgumentException('Unable to determine asset type.');
    }

    /**
     * Registers an asset type.
     *
     * @param  string $name
     * @param  string $class
     * @return self
     */
    public function register($name, $class)
    {
        $this->types[$name] = $class;

        return $this;
    }
}
