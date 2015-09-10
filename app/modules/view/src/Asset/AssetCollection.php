<?php

namespace Pagekit\View\Asset;

class AssetCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var AssetInterface[]
     */
    protected $assets;

    /**
     * Constructor.
     *
     * @param array $assets
     */
    public function __construct(array $assets = [])
    {
        $this->assets = $assets;
    }

    /**
     * Gets asset from collection.
     *
     * @param  string $name
     * @return AssetInterface
     */
    public function get($name)
    {
        return isset($this->assets[$name]) ? $this->assets[$name] : null;
    }

    /**
     * Adds asset to collection.
     *
     * @param AssetInterface $asset
     */
    public function add(AssetInterface $asset)
    {
        $this->assets[$asset->getName()] = $asset;
    }

    /**
     * Replace asset in collection.
     *
     * @param string         $name
     * @param AssetInterface $asset
     */
    public function replace($name, AssetInterface $asset)
    {
        $assets = [];

        foreach ($this->assets as $key => $val) {
            if ($key == $name) {
                $assets[$asset->getName()] = $asset;
            } else {
                $assets[$key] = $val;
            }
        }

        $this->assets = $assets;
    }

    /**
     * Removes assets from collection.
     *
     * @param string|array $name
     */
    public function remove($name)
    {
        $names = (array) $name;

        foreach ($names as $name) {
            unset($this->assets[$name]);
        }
    }

    /**
     * Gets the unique hash of the collection.
     *
     * @param  string $salt
     * @return string
     */
    public function hash($salt = '')
    {
        $hashes = [];

        foreach ($this as $asset) {
            $hashes[] = $asset->hash($salt);
        }

        return hash('crc32b', implode('', $hashes));
    }

    /**
     * Dumps collection to a string.
     *
     * @param  array $filters
     * @return string
     */
    public function dump(array $filters = [])
    {
        $data = '';

        foreach ($this as $asset) {
            $data .= $asset->dump($filters)."\n\n";
        }

        return $data;
    }

    /**
     * Gets all asset names.
     *
     * @return int
     */
    public function names()
    {
        return array_keys($this->assets);
    }

    /**
     * Countable interface implementation.
     *
     * @return int
     */
    public function count()
    {
        return count($this->assets);
    }

    /**
     * IteratorAggregate interface implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->assets);
    }
}
