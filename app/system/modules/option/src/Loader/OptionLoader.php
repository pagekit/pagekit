<?php

namespace Pagekit\Option\Loader;

use Pagekit\Application as App;
use Pagekit\Module\Loader\LoaderInterface;

class OptionLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load($name, array $config)
    {
        if (is_array($options = App::option("$name:config", []))) {
            $config = array_replace_recursive($config, ['config' => $options]);
        }

        return $config;
    }
}
