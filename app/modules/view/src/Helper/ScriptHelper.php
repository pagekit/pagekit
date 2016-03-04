<?php

namespace Pagekit\View\Helper;

use Pagekit\View\Asset\AssetManager;
use Pagekit\View\View;

class ScriptHelper implements HelperInterface, \IteratorAggregate
{
    /**
     * @var AssetManager
     */
    protected $scripts;

    /**
     * Constructor.
     *
     * @param AssetManager $scripts
     */
    public function __construct(AssetManager $scripts = null)
    {
        $this->scripts = $scripts ?: new AssetManager();
    }

    /**
     * {@inheritdoc}
     */
    public function register(View $view)
    {
        $view->on('head', function ($event) use ($view) {
            $view->trigger('scripts', [$this->scripts]);
            $event->addResult($this->render());
        }, 5);
    }

    /**
     * Add shortcut.
     *
     * @see add()
     */
    public function __invoke($name, $source = null, $dependencies = [], $options = [])
    {
        return $this->scripts->add($name, $source, $dependencies, $options);
    }

    /**
     * Proxies all method calls to the manager.
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!is_callable($callable = [$this->scripts, $method])) {
            throw new \InvalidArgumentException(sprintf('Undefined method call "%s::%s"', get_class($this->scripts), $method));
        }

        return call_user_func_array($callable, $args);
    }

    /**
     * Renders the script tags.
     *
     * @return string
     */
    public function render()
    {
        $output = '';

        foreach ($this->scripts as $script) {
            if ($source = $script->getSource()) {

                $attributes = '';
                foreach (['async', 'defer'] as $attribute) {
                    $attributes .= $script->getOption($attribute) ? ' ' . $attribute : '';
                }

                $output .= sprintf("        <script src=\"%s\"%s></script>\n", $source, $attributes);
            } elseif ($content = $script->getContent()) {
                $output .= sprintf("        <script>%s</script>\n", $content);
            }
        }

        return $output;
    }

    /**
     * Returns an iterator for script tags.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->scripts->getIterator();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'script';
    }
}
