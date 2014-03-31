<?php

namespace Pagekit\Widget\Event;

use Pagekit\Component\View\View;
use Pagekit\Framework\Event\Event;
use Pagekit\Widget\PositionRenderer\CallbackPositionRenderer;
use Pagekit\Widget\PositionRenderer\LayoutPositionRenderer;
use Pagekit\Widget\PositionRenderer\PositionRendererInterface;

class RegisterRendererEvent extends Event
{
    /**
     * @var View
     */
    protected $view;

    /**
     * @var PositionRendererInterface[]
     */
    protected $parameters = array();

    /**
     * Constructor.
     *
     * @param View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * Registers a position renderer.
     *
     * @param string $name
     * @param callable|string|PositionRendererInterface $renderer
     * @throws \InvalidArgumentException
     */
    public function register($name, $renderer)
    {
        if (is_callable($renderer)) {
            $renderer = new CallbackPositionRenderer($renderer);
        }

        if (is_string($renderer)) {
            $renderer = new LayoutPositionRenderer($this->view, $renderer);
        }

        if (!$renderer instanceof PositionRendererInterface) {
            throw new \InvalidArgumentException('Renderer has to implement the PositionRendererInterface');
        }

        $this->parameters[$name] = $renderer;
    }
}
