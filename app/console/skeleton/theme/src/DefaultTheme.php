<?php

namespace %NAMESPACE%;

use Pagekit\Application as App;
use Pagekit\System\Theme;

class %CLASSNAME% extends Theme
{
    /**
     * {@inheritdoc}
     */
    public function boot(App $app)
    {
        parent::boot($app);

        $app->on('site', function($event, $app) {

            // your code here...

        });
    }
}
