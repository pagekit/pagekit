<?php

namespace Pagekit\System;

use Pagekit\Framework\Application;
use Symfony\Component\Finder\Finder;

class FileProvider
{
    protected $app;

    /**
     * Constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Find files and directories using the Symfony Finder.
     *
     * @return Finder
     */
    public function find()
    {
        return Finder::create();
    }

    /**
     * Locate the fully qualified path.
     *
     * @param  string $name
     * @return string|false
     */
    public function locate($name)
    {
        return $this->app['locator']->findResource($name);
    }

    /**
     * Proxy method call to filesystem.
     *
     * @param  string $method
     * @param  array $args
     * @throws \BadMethodCallException
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->app['files'], $method)) {
            throw new \BadMethodCallException(sprintf('Undefined method call "%s::%s"', get_class($this->app['files']), $method));
        }

        return call_user_func_array([$this->app['files'], $method], $args);
    }
}
