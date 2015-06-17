<?php

namespace Pagekit\Module\Factory;

use Pagekit\Application;

class ModuleFactory implements FactoryInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     *
     * @param Application $app
     * @param string      $class
     */
    public function __construct(Application $app, $class = 'Pagekit\\Module\\Module')
    {
        $this->app = $app;
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $module)
    {
        $class = is_string($module['main']) ? $module['main'] : $this->class;

        $module = new $class($module);
        $module->main($this->app);

        return $module;
    }
}
