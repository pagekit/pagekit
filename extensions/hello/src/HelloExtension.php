<?php

namespace Pagekit\Hello;

use Pagekit\Application as App;
use Pagekit\System\Extension;

class HelloExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
    }

    public function enable()
    {
        // run all migrations that are newer than the current version
        if ($version = App::migrator()->create('hello:migrations', $this->config('version'))->run()) {
            App::config($this->name)->set('version', $version);
        }
    }

    public function disable()
    {
        // do nothing
    }

    public function uninstall()
    {
        // downgrade all migrations
        App::migrator()->create('hello:migrations', $this->config('version'))->run(0);

        // remove the config
        App::config()->remove($this->name);
    }
}
