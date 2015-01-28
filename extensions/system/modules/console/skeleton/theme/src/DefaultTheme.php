<?php

namespace %NAMESPACE%;

use Pagekit\Application as App;
use Pagekit\Theme\Theme;

class %CLASSNAME% extends Theme
{
    /**
     * {@inheritdoc}
     */
    public function boot(App $app)
    {
        parent::boot($app);

        $app->on('system.site', function() use ($app) {

            // your code here...

        });
    }
}
