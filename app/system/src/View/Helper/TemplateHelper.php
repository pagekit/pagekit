<?php

namespace Pagekit\System\View\Helper;

use Pagekit\Application;
use Pagekit\View\ViewInterface;
use Pagekit\View\Helper\HelperInterface;

class TemplateHelper implements HelperInterface
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
     * Constructor.
     *
     * @param ViewInterface $view
     */
    public function __construct(ViewInterface $view, Application $app)
    {
        $view->on('head', function ($event) use ($view, $app) {
            if ($templates = $this->queued()) {
                $view->script('tmpl', $app['url']->get('@system/system/tmpls', ['templates' => implode(',', $templates)]));
            }
        }, 10);
    }

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
     * Adds one or more registered template to the queue.
     *
     * @param string|array $name
     */
    public function add($name)
    {
        $templates = (array) $name;

        foreach ($templates as $name) {
            if (isset($this->templates[$name])) {
                $this->queued[$name] = true;
            }
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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tmpl';
    }
}
