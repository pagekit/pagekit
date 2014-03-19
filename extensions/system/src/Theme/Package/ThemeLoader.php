<?php

namespace Pagekit\Theme\Package;

use Pagekit\Component\Package\Exception\UnexpectedValueException;
use Pagekit\Component\Package\Loader\JsonLoader;

class ThemeLoader extends JsonLoader
{
    /**
     * @var ExtensionLoader
     */
    protected $loader;

    /**
     * {@inheritdoc}
     */
    protected function loadConfig(array $config, $class)
    {
        $package = parent::loadConfig($config, $class);

        if ($package->getType() != 'theme') {
            throw new UnexpectedValueException('Package '.$config['name'].' has no type "theme" defined.');
        }

        return $package;
    }
}
