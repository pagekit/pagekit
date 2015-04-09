<?php

namespace Pagekit\Page;

use Pagekit\Application as App;
use Pagekit\Page\Event\SiteListener;
use Pagekit\System\Extension;

class PageExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app->subscribe(new SiteListener);
    }

    /**
     * {@inheritdoc}
     */
    public function enable()
    {
        if ($version = App::migrator()->create('extensions/page/migrations', App::option('page:version'))->run()) {
            App::option()->set('page:version', $version);
        }
    }
}
