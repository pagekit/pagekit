<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\Event;

class TmplEvent extends Event
{
    /**
     * @var string[]
     */
    private $templates = [];

    /**
     * Checks if a template is registered.
     *
     * @param  string $name
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->templates[$name]);
    }

    /**
     * Returns a template.
     *
     * @param  string $name
     * @return string
     */
    public function get($name)
    {
        return $this->has($name) ? $this->templates[$name] : null;
    }

    /**
     * Registers template.
     *
     * @param  string $name
     * @param  string $template
     */
    public function register($name, $template)
    {
        $this->templates[$name] = $template;
    }

    /**
     * Unregisters a template.
     *
     * @param  string  $name
     */
    public function unregister($name)
    {
        unset($this->templates[$name]);
    }
}
