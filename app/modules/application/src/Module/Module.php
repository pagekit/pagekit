<?php

namespace Pagekit\Module;

use Pagekit\Application as App;

class Module
{
    /**
     * Constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        foreach ($values as $key => $value) {
            if ($value instanceof \Closure) {
                $this->$key = $value->bindTo($this);
            } else {
                $this->$key = $value;
            }
        }

        if (!isset($this->config)) {
            $this->config = [];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        if (is_callable($this->main)) {
            return call_user_func($this->main, $app);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function config($key = null, $default = null)
    {
        if (null === $key) {
            return $this->config;
        }

        $array = $this->config;

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
}
