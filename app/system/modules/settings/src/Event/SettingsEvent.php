<?php

namespace Pagekit\System\Event;

use Pagekit\Application as App;
use Pagekit\Event\Event;
use Pagekit\Util\Arr;

class SettingsEvent extends Event
{
    /**
     * @var array
     */
    protected $sections = [];

    /**
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Adds settings section.
     *
     * @param string          $name
     * @param string          $label
     * @param string|callable $section
     */
    public function section($name, $label, $section)
    {
        $this->sections[$name] = ['label' => $label, 'view' => is_callable($section) ? call_user_func($section) : App::view($section)];
    }

    /**
     * Adds settings options.
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
     * Adds settings config.
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
     * Adds settings data.
     *
     * @param string $name
     * @param mixed  $data
     * @param array  $keys
     */
    public function data($name, $data, array $keys = null)
    {
        if ($data = is_array($data) ? $this->filter($data, $keys) : $data) {
            App::view()->data('$settings', [$name => $data]);
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

        $data = Arr::flatten($data);

        $result = [];
        foreach($data as $keypath => $value) {
            foreach ($keys as $key) {
                if (0 === strpos($keypath, $key)) {
                    $result[$keypath] = $value;
                }
            }
        }

        return Arr::expand($result);
    }
}
