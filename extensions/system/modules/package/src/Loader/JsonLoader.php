<?php

namespace Pagekit\Package\Loader;

use Pagekit\Package\Exception\InvalidArgumentException;
use Pagekit\Package\PackageInterface;

class JsonLoader extends ArrayLoader
{
    /**
     * @param  mixed  $json A file or json string
     * @param  string $class
     * @throws InvalidArgumentException
     * @return PackageInterface
     */
    public function load($json, $class = 'Pagekit\Package\Package')
    {
        $json = (string) $json;

        if (strpos($json, '{') !== false && !file_exists($json)) {
            $config = json_decode($json, true);
        } elseif (file_exists($json)) {
            $config = json_decode(file_get_contents($json), true);
        }

        if (!isset($config) || !is_array($config)) {
            throw new InvalidArgumentException('Unable to load json.');
        }

        return $this->loadConfig($config, $class);
    }

    /**
     * Create package from array config.
     */
    protected function loadConfig(array $config, $class)
    {
        return parent::load($config, $class);
    }
}
