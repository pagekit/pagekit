<?php

namespace Pagekit\Config\Loader;

use Pagekit\Application as App;
use Pagekit\Module\Loader\LoaderInterface;

class ConfigLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load($name, array $config)
    {
        if (is_array($options = App::config($name, []))) {
            $config = array_replace_recursive($config, ['config' => $options]);
        }

        return $config;
    }
}
