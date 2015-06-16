<?php

namespace Pagekit\Widget;

use Pagekit\Application;
use Pagekit\Module\Loader\ModuleLoader;

class WidgetLoader extends ModuleLoader
{
    /**
     * Constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app, 'widget');
    }
}
