<?php

namespace Pagekit\View\Helper;

use Pagekit\View\ViewInterface;
use Pagekit\View\Asset\AssetManager;

class ScriptHelper implements HelperInterface, \IteratorAggregate
{
    /**
     * @var AssetManager
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param ViewInterface $view
     * @param AssetManager  $manager
     */
    public function __construct(ViewInterface $view, AssetManager $manager = null)
    {
        $this->manager = $manager ?: new AssetManager();

        $view->on('head', function ($event) {
            $event->setResult($event->getResult().$this->render());
        }, 5);
    }

    /**
     * Add shortcut.
     *
     * @see add()
     */
    public function __invoke($name, $source = null, $dependencies = [], $options = [])
    {
        return $this->manager->add($name, $source, $dependencies, $options);
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
        if (!is_callable($callable = [$this->manager, $method])) {
            throw new \InvalidArgumentException(sprintf('Undefined method call "%s::%s"', get_class($this->manager), $method));
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

        foreach ($this->manager as $script) {
            if ($source = $script->getSource()) {
                $output .= sprintf("        <script src=\"%s\"></script>\n", $source);
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
        return $this->manager->getIterator();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'script';
    }
}
