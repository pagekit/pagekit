<?php

namespace Pagekit\Module;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;
use Pagekit\Util\Arr;

class Module implements ModuleInterface, EventSubscriberInterface
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $path;

    /**
     * @var array
     */
    public $config;

    /**
     * @var array
     */
    public $options;

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->name = $options['name'];
        $this->path = $options['path'];
        $this->config = $options['config'];
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $main = $this->options['main'];

        if ($main instanceof \Closure) {
            $main = $main->bindTo($this, $this);
        }

        if (is_callable($main)) {
            return call_user_func($main, $app);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        if (is_array($key)) {
            return Arr::extract($this->options, $key);
        }

        return Arr::get($this->options, $key, $default);
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
        return isset($this->options['events']) ? $this->options['events'] : [];
    }
}
