<?php

namespace Pagekit\Config;

use Pagekit\Config\Loader\LoaderInterface;
use Pagekit\Config\Loader\PhpLoader;

class Config implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var array
     */
    protected $replace = [];

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * Create a new configuration.
     *
     * @param array           $replace
     * @param LoaderInterface $loader
     */
    public function __construct(array $replace = [], LoaderInterface $loader = null)
    {
        foreach ($replace as $name => $value) {
            $this->replace['%'.$name.'%'] = $value;
        }

        $this->loader = $loader ?: new PhpLoader;
    }

    /**
     * Get shortcut.
     *
     * @see get()
     */
    public function __invoke($key, $default = null)
    {
        return $this->get($key, $default);
    }

    /**
     * Get all configuration values.
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string $key
     * @return bool
     */
    public function has($key)
    {
        $default = microtime(true);

        return $this->get($key, $default) != $default;
    }

    /**
     * Get the specified configuration value.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $array = $this->values;

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {

            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Set a given configuration value.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $keys = explode('.', $key);
        $array =& $this->values;

        while (count($keys) > 1) {

            $key = array_shift($keys);

            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get a configuration value.
     *
     * @param  string $key
     * @return bool
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a configuration value.
     *
     * @param string $key
     * @param string $value
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration value.
     *
     * @param string $key
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }

    /**
     * Load a configuration file or array of files.
     *
     * @param string|array $files
     */
    public function load($files)
    {
        foreach ((array) $files as $file) {

            $values = $this->loader->load($file);

            foreach ($values as $name => $value) {
                if ('%' === substr($name, 0, 1)) {
                    $this->replace[$name] = (string) $value;
                }
            }

            $this->values = $this->merge($this->values, $values);
        }
    }

    /**
     * Dumps the configuration values.
     *
     * @return string
     */
    public function dump()
    {
        return '<?php return '.var_export($this->values, true).';';
    }

    /**
     * Merge two arrays recursively and overwrite existing keys.
     *
     * @param  array $current
     * @param  array $new
     * @return array
     */
    protected function merge(array $current, array $new)
    {
        foreach ($new as $key => $value) {
            if (isset($current[$key]) && is_array($value)) {
                $current[$key] = $this->merge($current[$key], $value);
            } else {
                $current[$key] = $this->replace($value);
            }
        }

        return $current;
    }

    /**
     * Replace "%foo%" placeholders with the actual value.
     *
     * @param  mixed $value
     * @return mixed
     */
    protected function replace($value)
    {
        if (!$this->replace) {
            return $value;
        }

        if (is_array($value)) {

            foreach ($value as $k => $v) {
                $value[$k] = $this->replace($v);
            }

            return $value;
        }

        if (is_string($value)) {
            return strtr($value, $this->replace);
        }

        return $value;
    }
}
