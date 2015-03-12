<?php

namespace Pagekit\View\Helper;

use Pagekit\Application as App;

class TemplateHelper
{
    /**
     * @var array
     */
    protected $templates = [];

    /**
     * @var array
     */
    protected $queued = [];

    /**
     * Add shortcut.
     *
     * @see add()
     */
    public function __invoke($name)
    {
        $this->add($name);
    }

    /**
     * Checks if a template is registered.
     *
     * @param  string  $name
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->templates[$name]);
    }

    /**
     * Gets a template.
     *
     * @param  string $name
     * @return string
     */
    public function get($name)
    {
        return $this->has($name) ? $this->templates[$name] : null;
    }

    /**
     * Adds a registered template to the queue.
     *
     * @param string $name
     */
    public function add($name)
    {
        if (isset($this->templates[$name])) {
            $this->queued[$name] = true;
        }
    }

    /**
     * Registers template.
     *
     * @param string $name
     * @param string $template
     */
    public function register($name, $template)
    {
        $this->templates[$name] = $template;
    }

    /**
     * Unregisters a template.
     *
     * @param string $name
     */
    public function unregister($name)
    {
        unset($this->templates[$name]);
    }

    /**
     * Gets the queued templates.
     *
     * @return string
     */
    public function queued()
    {
        return array_keys($this->queued);
    }
}
