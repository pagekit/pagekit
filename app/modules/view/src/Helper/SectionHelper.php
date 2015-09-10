<?php

namespace Pagekit\View\Helper;

use Pagekit\View\View;

class SectionHelper extends Helper
{
    /**
     * @var array
     */
    protected $sections = [];

    /**
     * @var array
     */
    protected $openSections = [];

    /**
     * {@inheritdoc}
     */
    public function register(View $view)
    {
        parent::register($view);

        $view->on('render', function ($event) {
            if ($this->exists($name = $event->getTemplate())) {
                $event->setResult($this->get($name));
            }
        }, 10);
    }

    /**
     * Set shortcut.
     *
     * @see set()
     */
    public function __invoke($name, $content)
    {
        $this->set($name, $content);
    }

    /**
     * Gets a section.
     *
     * @param  string $name
     * @return string
     */
    public function get($name)
    {
        return isset($this->sections[$name]) ? $this->sections[$name] : '';
    }

    /**
     * Sets a section value.
     *
     * @param string $name
     * @param mixed  $content
     */
    public function set($name, $content)
    {
        $this->sections[$name] = $content;
    }

    /**
     * Checks if the section exists.
     *
     * @param  string $name
     * @return bool
     */
    public function exists($name)
    {
        return isset($this->sections[$name]);
    }

    /**
     * Starts a new section.
     *
     * @param  string $name
     * @throws \InvalidArgumentException
     */
    public function start($name)
    {
        if (in_array($name, $this->openSections)) {
            throw new \InvalidArgumentException(sprintf('A section named "%s" is already started.', $name));
        }

        $this->openSections[] = $name;
        $this->sections[$name] = '';

        ob_start();
        ob_implicit_flush(0);
    }

    /**
     * Stops a section.
     *
     * @param  bool $show
     * @throws \LogicException
     */
    public function stop($show = false)
    {
        if (!$this->openSections) {
            throw new \LogicException('No section started.');
        }

        $name = array_pop($this->openSections);

        $this->sections[$name] = ob_get_clean();

        if ($show) {
            echo $this->view->render($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'section';
    }
}
