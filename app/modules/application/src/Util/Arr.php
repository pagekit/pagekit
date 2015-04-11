<?php

namespace Pagekit\Util;

class Arr
{
    const ARRAY_FILTER_USE_BOTH = 1;
    const ARRAY_FILTER_USE_KEY  = 2;

    /**
     * Checks if the given key exists.
     *
     * @param  array  $array
     * @param  string $key
     * @return bool
     */
    public static function has($array, $key)
    {
        if (!$array || $key === null) {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return true;
        }

        $parts = explode('.', $key);

        foreach ($parts as $part) {

            if (!is_array($array) || !array_key_exists($part, $array)) {
                return false;
            }

            $array = $array[$part];
        }

        return true;
    }

    /**
     * Gets a value by key.
     *
     * @param  array  $array
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        if ($key === null) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        $parts = explode('.', $key);

        foreach ($parts as $part) {

            if (!is_array($array) || !array_key_exists($part, $array)) {
                return $default;
            }

            $array = $array[$part];
        }

        return $array;
    }

    /**
     * Sets a value.
     *
     * @param  array  $array
     * @param  string $key
     * @param  mixed  $value
     * @return array
     */
    public static function set(&$array, $key, $value)
    {
        if ($key === null) {
            return $array = $value;
        }

        $parts = explode('.', $key);

        while (count($parts) > 1) {

            $part = array_shift($parts);

            if (!isset($array[$part]) || !is_array($array[$part])) {
                $array[$part] = [];
            }

            $array =& $array[$part];
        }

        $array[array_shift($parts)] = $value;

        return $array;
    }

    /**
     * Removes a value from array by key.
     *
     * @param array        $array
     * @param array|string $keys
     */
    public static function remove(&$array, $keys)
    {
        $original =& $array;

        foreach ((array) $keys as $key) {

            $parts = explode('.', $key);

            while (count($parts) > 1) {

                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array =& $array[$part];
                }
            }

            unset($array[array_shift($parts)]);

            $array =& $original;
        }
    }

    /**
     * Removes a value from array.
     *
     * @param  array $array
     * @param  mixed $value
     * @param  bool  $strict
     * @return array
     */
    public static function pull(&$array, $value, $strict = false)
    {
        if ($keys = array_keys($array, $value, $strict)) {

            foreach ($keys as $key) {
                unset($array[$key]);
            }

            $array =& array_values($array);
        }

        return $array;
    }

    /**
     * Recursively merges two arrays.
     *
     * @param  array $array1
     * @param  array $array2
     * @param  bool  $replace
     * @return array
     */
    public static function merge($array1, $array2, $replace = false)
    {
        if ($replace) {
            return array_replace_recursive($array1, $array2);
        }

        foreach ($array2 as $key => $value) {
            if (isset($array1[$key]) && is_array($value)) {
                $array1[$key] = static::merge($array1[$key], $value);
            } else {
                $array1[$key] = $value;
            }
        }

        return $array1;
    }

    /**
     * Filters an array using a callback function.
     *
     * @param  array    $array
     * @param  callable $callback
     * @param  int      $flag
     * @return array
     */
    public static function filter($array, callable $callback, $flag = 2)
    {
        if (version_compare(PHP_VERSION, '5.6.0') >= 0) {
            return array_filter($array, $callback, $flag);
        }

        $filtered = [];

        foreach ($array as $key => $value) {

            $args = [$value];

            if ($flag === static::ARRAY_FILTER_USE_BOTH) {
                $args = [$value, $key];
            }

            if ($flag === static::ARRAY_FILTER_USE_KEY) {
                $args = [$key];
            }

            if (call_user_func_array($callback, $args)) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }
}
