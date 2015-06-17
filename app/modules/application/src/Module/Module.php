<?php

namespace Pagekit\Module;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;
use Pagekit\Util\Arr;

class Module implements ModuleInterface, EventSubscriberInterface
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
                $this->$key = $value->bindTo($this, $this);
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
        if (is_array($key)) {
            return Arr::extract($this->config, $key);
        }

        return Arr::get($this->config, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return isset($this->events) ? $this->events : [];
    }

}
