<?php

namespace Pagekit\Page;

use Pagekit\Extension\Extension;
use Pagekit\Framework\Application;
use Pagekit\Page\Event\RouteListener;

class PageExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        $app['events']->addSubscriber(new RouteListener);

        parent::boot($app);
    }
}
