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
        if (is_callable($view)) {
            $view = call_user_func($view);
        }

        $this->views[$name] = ['label' => $label, 'view' => $view];
    }

    /**
     * Add settings options.
     *
     * @param string $name
     * @param array  $data
     * @param array  $keys
     */
    public function options($name, $data, array $keys = null)
    {
        $this->data('option', [$name => $this->filter($data, $keys)]);
    }

    /**
     * Add settings config.
     *
     * @param string $name
     * @param array  $data
     * @param array  $keys
     */
    public function config($name, $data, array $keys = null)
    {
        $this->data('config', [$name => $this->filter($data, $keys)]);
    }

    /**
     * Add settings data.
     *
     * @param string $name
     * @param array  $data
     * @param array  $keys
     */
    public function data($name, $data, array $keys = null)
    {
        App::view()->data('settings', [$name => $this->filter($data, $keys)]);
    }

    /**
     * Filters data by keys.
     *
     * @param  array $data
     * @param  array $keys
     * @return array
     */
    protected function filter($data, array $keys = null)
    {
        if (!$keys) {
            return $data;
        }

        return array_intersect_key($data, array_flip($keys));
    }
}
