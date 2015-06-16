<?php

namespace Pagekit\System\Loader;

use Pagekit\Application;
use Pagekit\Module\Loader\ModuleLoader;

class ExtensionLoader extends ModuleLoader
{
    /**
     * Constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app, 'extension', 'Pagekit\\System\\Extension');
    }
}
