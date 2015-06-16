<?php

namespace Pagekit\Module\Loader;

use Pagekit\Application;

class ModuleLoader implements LoaderInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     *
     * @param Application $app
     * @param string      $type
     * @param string      $class
     */
    public function __construct(Application $app, $type = 'module', $class = 'Pagekit\\Module\\Module')
    {
        $this->app = $app;
        $this->type = $type;
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function load($name, $module)
    {
        if (!is_array($module) || $module['type'] != $this->type) {
            return $module;
        }

        $class = is_string($module['main']) ? $module['main'] : $this->class;

        $module = new $class($module);
        $module->main($this->app);

        return $module;
    }
}
