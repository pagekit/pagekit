<?php

namespace Pagekit\Widget;

use Pagekit\Application;
use Pagekit\Module\ModuleManager;

class WidgetManager extends ModuleManager
{
    /**
     * Constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->defaults['class'] = 'Pagekit\Widget\Model\Type';
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        $this->load();

        return parent::get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $this->load();

        return parent::all();
    }

    /**
     * {@inheritdoc}
     */
    public function load($modules = null)
    {
        $this->registerModules();

        return parent::load(array_keys($this->registered));
    }
}
