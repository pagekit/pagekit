<?php

namespace Pagekit\View\Helper;

use Pagekit\View\Asset\AssetManager;
use Pagekit\View\View;

class StyleHelper implements HelperInterface, \IteratorAggregate
{
    /**
     * @var AssetManager
     */
    protected $styles;

    /**
     * Constructor.
     *
     * @param AssetManager $styles
     */
    public function __construct(AssetManager $styles = null)
    {
        $this->styles = $styles ?: new AssetManager();
    }

    /**
     * {@inheritdoc}
     */
    public function register(View $view)
    {
        $view->on('head', function ($event) use ($view) {
            $view->trigger('styles', [$this->styles]);
            $event->addResult($this->render());
        }, 15);
    }

    /**
     * Add shortcut.
     *
     * @see add()
     */
    public function __invoke($name, $source = null, $dependencies = [], $options = [])
    {
        return $this->styles->add($name, $source, $dependencies, $options);
    }

    /**
     * Proxies all method calls to the manager.
     *
     * @param  string $method
     * @param  array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!is_callable($callable = [$this->styles, $method])) {
            throw new \InvalidArgumentException(sprintf('Undefined method call "%s::%s"', get_class($this->styles), $method));
        }

        return call_user_func_array($callable, $args);
    }

    /**
     * Renders the style tags.
     *
     * @return string
     */
    public function render()
    {
        $output = '';

        foreach ($this->styles as $style) {
            if ($source = $style->getSource()) {
                $output .= sprintf("        <link href=\"%s\" rel=\"stylesheet\">\n", $source);
            } elseif ($content = $style->getContent()) {
                $output .= sprintf("        <style>%s</style>\n", $content);
            }
        }

        return $output;
    }

    /**
     * Returns an iterator for style tags.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->styles->getIterator();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'style';
    }
}
