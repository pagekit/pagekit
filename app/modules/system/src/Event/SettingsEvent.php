<?php

namespace Pagekit\System\Event;

use Pagekit\Application as App;
use Pagekit\Event\Event;

class SettingsEvent extends Event
{
    /**
     * @var array
     */
    protected $views = [];

    /**
     * @return array
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Add settings view.
     *
     * @param string          $name
     * @param string          $label
     * @param string|callable $view
     */
    public function view($name, $label, $view)
    {
        $this->views[$name] = ['label' => $label, 'view' => is_callable($view) ? call_user_func($view) : App::view($view)];
    }

    /**
     * Add settings options.
     *
     * @param string $name
     * @param array  $data
     * @param array  $keys
     */
    public function options($name, array $data, array $keys = null)
    {
        if ($data = $this->filter($data, $keys)) {
            $this->data('option', [$name => $data]);
        }
    }

    /**
     * Add settings config.
     *
     * @param string $name
     * @param array  $data
     * @param array  $keys
     */
    public function config($name, array $data, array $keys = null)
    {
        if ($data = $this->filter($data, $keys)) {
            $this->data('config', [$name => $data]);
        }
    }

    /**
     * Add settings data.
     *
     * @param string $name
     * @param mixed  $data
     * @param array  $keys
     */
    public function data($name, $data, array $keys = null)
    {
        if ($data = is_array($data) ? $this->filter($data, $keys) : $data) {
            App::view()->data('settings', [$name => $data]);
        }
    }

    /**
     * Filters data by keys.
     *
     * @param  array $data
     * @param  array $keys
     * @return array
     */
    protected function filter(array $data, array $keys = null)
    {
        if (!$keys) {
            return $data;
        }

        $data = $this->flatten($data);

        $result = [];
        foreach($data as $keypath => $value) {
            foreach ($keys as $key) {
                if (0 === strpos($keypath, $key)) {
                    $result[$keypath] = $value;
                }
            }
        }

        return $this->expand($result);
    }

    /**
     * Flattens an array.
     *
     * @param  array $array
     * @param  string $path
     * @return array
     */
    protected function flatten(array $array, $path = '')
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $results = array_merge($results, $this->flatten($value, $path.$key.'.'));
            } else {
                $results[$path.$key] = $value;
            }
        }

        return $results;
    }

    /**
     * Expands an array.
     *
     * @param  array $values
     * @return array
     */
    protected function expand(array $values)
    {
        $result = [];
        foreach ($values as $key => $value) {

            $array =& $result;
            $keys = explode('.', $key);
            while (count($keys) > 1) {

                $key = array_shift($keys);

                if (!isset($array[$key]) || !is_array($array[$key])) {
                    $array[$key] = [];
                }

                $array =& $array[$key];
            }

            $array[array_shift($keys)] = $value;
        }
        return $result;
    }
}
