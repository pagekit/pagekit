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
        if (is_array($settings = App::option("$name:settings", []))) {
            $config = array_replace_recursive($config, ['config' => $settings]);
        }

        return $config;
    }
}
