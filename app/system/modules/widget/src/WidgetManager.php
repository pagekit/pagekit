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
        $this->load(array_keys($this->registered));

        return parent::get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $this->load(array_keys($this->registered));

        return parent::all();
    }
}
