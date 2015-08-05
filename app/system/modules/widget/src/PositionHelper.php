<?php

namespace Pagekit\Widget;

use Pagekit\View\Helper\Helper;

class PositionHelper extends Helper
{
    protected $positions;

    public function __construct(PositionManager $positions)
    {
        $this->positions = $positions;
    }

    /**
     * Set shortcut.
     *
     * @see render()
     */
    public function __invoke($name, $view = null, array $parameters = [])
    {
        return $this->render($name, $view, $parameters);
    }

    /**
     * Checks if the position exists.
     *
     * @param  string $name
     * @return bool
     */
    public function exists($name)
    {
        return (bool) $this->positions->findActive($name);
    }

    /**
     * Renders a position.
     *
     * @param  string       $name
     * @param  array|string $view
     * @param  array        $parameters
     * @return string
     */
    public function render($name, $view = null, array $parameters = [])
    {
        if (is_array($view)) {
            $parameters = $view;
            $view = false;
        }

        $parameters['widgets'] = $this->positions->findActive($name);

        return $this->view->render($view ?: 'system/site/position.php', $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'position';
    }
}
