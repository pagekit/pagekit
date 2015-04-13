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

        if (!$app['config']->get($this->name)) {
            $app['config']->set($this->name, [], true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function enable()
    {
        $config = App::config($this->name);

        if ($version = App::migrator()->create('extensions/page/migrations', $config->get('version'))->run()) {
            $config->set('version', $version);
        }
    }
}
