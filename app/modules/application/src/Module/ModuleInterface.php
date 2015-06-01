<?php

namespace Pagekit\Module;

use Pagekit\Application as App;

interface ModuleInterface
{
    /**
     * Main bootstrap method.
     *
     * @param  App $app
     * @return mixed
     */
    public function main(App $app);

    /**
     * Gets a config setting.
     *
     * @param  mixed $key
     * @param  mixed $default
     * @return mixed
     */
    public function config($key = null, $default = null);
}
