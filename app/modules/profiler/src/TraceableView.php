<?php

namespace Pagekit\Profiler;

use Pagekit\View\ViewInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Collects some data about views.
 */
class TraceableView implements ViewInterface
{
    private $stopwatch;

    /**
     * Constructor.
     *
     * @param ViewInterface   $view
     * @param Stopwatch       $stopwatch
     */
    public function __construct(ViewInterface $view, Stopwatch $stopwatch)
    {
        $this->view      = $view;
        $this->stopwatch = $stopwatch;
    }

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
     * Proxies all method calls to the original view.
     *
     * @param string $method    The method name
     * @param array  $arguments The method arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->view, $method], $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function render($name, array $parameters = [])
    {
        $e = $this->stopwatch->start($name, 'views');

        $result = $this->view->render($name, $parameters);

        $e->stop();

        return $result;
    }
}
