<?php

namespace Pagekit\Option\Loader;

use Pagekit\Application as App;
use Pagekit\Module\Config\LoaderInterface;

class OptionLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load($name, array $config)
    {
        return array_replace_recursive($config, App::option("$name:settings", []));
    }
}
