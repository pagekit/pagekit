<?php

namespace %NAMESPACE%;

use Pagekit\Application;
use Pagekit\Theme\Theme;

class %CLASSNAME% extends Theme
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        parent::boot($app);

        $app->on('system.site', function() use ($app) {

            // your code here...

        });
    }
}
