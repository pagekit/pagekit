<?php

namespace Pagekit\View\Helper;

use Pagekit\View\ViewInterface;

class MapHelper implements HelperInterface, \IteratorAggregate
{
    /**
     * @var array
     */
    protected $map = [];

    /**
     * Constructor.
     *
     * @param ViewInterface $view
     */
    public function __construct(ViewInterface $view)
    {
        $view->on('render', function ($event) {
            if ($this->has($template = $event->getTemplate())) {
                $event->setTemplate($this->get($template));
            }
        }, 10);
    }

    /**
     * Add shortcut.
     *
     * @see add()
     */
    public function __invoke($name, $path = null)
    {
        return $this->add($name, $path);
    }

    /**
     * Gets a template.
     *
     * @param  string $name
     * @return string
     */
    public function get($name)
    {
        return isset($this->map[$name]) ? $this->map[$name] : null;
    }

    /**
     * Adds a template.
     *
     * @param string|array $name
     * @param string       $path
     */
    public function add($name, $path = null)
    {
        if (is_string($name) && $path) {
            $this->map[$name] = $path;
        } elseif (is_array($name)) {
            foreach ($name as $key => $path) {
                $this->map[$key] = $path;
            }
        }
    }

    /**
     * Checks if the template exists.
     *
     * @param  string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->map[$name]);
    }

    /**
     * Implements the IteratorAggregate.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->map);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'map';
    }
}
