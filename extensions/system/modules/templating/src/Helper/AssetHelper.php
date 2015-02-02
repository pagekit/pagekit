<?php

namespace Pagekit\Templating\Helper;

use Pagekit\View\Asset\AssetManager;
use Symfony\Component\Templating\Helper\Helper;

abstract class AssetHelper extends Helper
{
    /**
     * @var AssetManager
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param AssetManager $manager
     */
    public function __construct(AssetManager $manager)
    {
        $this->manager = $manager;
    }

    public function __call($method, $args)
    {
        if (!is_callable($callable = [$this->manager, $method])) {
            throw new \InvalidArgumentException(sprintf('Undefined method call "%s::%s"', get_class($this->manager), $method));
        }

        return call_user_func_array($callable, $args);
    }
}
