<?php

namespace Pagekit\Templating;

use Symfony\Component\Templating\DelegatingEngine;

class TemplateEngine extends DelegatingEngine
{
    /**
     * @var array
     */
    protected $globals = [];

    /**
     * Render shortcut.
     *
     * @see render()
     */
    public function __invoke($name, array $parameters = [])
    {
        return $this->render($name, $parameters);
    }

    /**
     * Gets the global parameters.
     *
     * @return array
     */
    public function getGlobals()
    {
        return $this->globals;
    }

    /**
     * Adds a global parameter.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function addGlobal($name, $value)
    {
        $this->globals[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function render($name, array $parameters = [])
    {
        return parent::render($name, array_replace($this->globals, $parameters));
    }
}
