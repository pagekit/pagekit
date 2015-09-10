<?php

namespace Pagekit\Filesystem;

class Locator
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $paths = [];

    /**
     * Constructor.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $path = strtr($path, '\\', '/');

        if (substr($path, -1) != '/') {
            $path .= '/';
        }

        $this->path = $path;
    }

    /**
     * Adds file paths to locator.
     *
     * @param  string       $prefix
     * @param  string|array $paths
     * @return self
     */
    public function add($prefix, $paths)
    {
        $paths = array_map(function($path) use ($prefix) {

            $path = strtr($path, '\\', '/');

            if (substr($path, -1) != '/') {
                $path .= '/';
            }

            return [$prefix, $path];
        }, (array) $paths);

        $this->paths = array_merge($paths, $this->paths);

        return $this;
    }

    /**
     * Gets a file path from locator.
     *
     * @param  string $file
     * @return string|false
     */
    public function get($file)
    {
        $file  = ltrim(strtr($file, '\\', '/'), '/');
        $paths = array_merge($this->paths, [['', $this->path]]);

        foreach ($paths as $parts) {

            list($prefix, $path) = $parts;

            if ($prefix !== '' && strpos($file, $prefix) !== 0) {
                continue;
            }

            if (($part = substr($file, strlen($prefix))) !== false) {
                $path .= ltrim($part, '/');
            }

            if (file_exists($path)) {
                return $path;
            }
        }

        return false;
    }
}
