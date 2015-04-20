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

        $app->on('app.site', function() use ($app) {

            // your code here...

        });
    }
}
