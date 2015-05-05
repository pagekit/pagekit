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
        if ($version = App::migrator()->create('extensions/hello/migrations', $this->config('version'))->run()) {
            App::config($this->name)->set('version', $version);
        }
    }

    public function disable()
    {
        // do nothing
    }

    public function uninstall()
    {
        // drop all own tables (created in migrations)
        $util = App::db()->getUtility();
        $util->dropTable('@hello_greetings');

        // remove the config
        App::config()->remove($this->name);
    }
}
