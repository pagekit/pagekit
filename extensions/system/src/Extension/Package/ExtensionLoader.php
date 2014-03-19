<?php

namespace Pagekit\Extension\Package;

use Pagekit\Component\Package\Exception\UnexpectedValueException;
use Pagekit\Component\Package\Loader\JsonLoader;

class ExtensionLoader extends JsonLoader
{
    /**
     * {@inheritdoc}
     */
    protected function loadConfig(array $config, $class)
    {
        $package = parent::loadConfig($config, $class);

        if ($package->getType() != 'extension') {
            throw new UnexpectedValueException('Package "'.$config['name'].'" has no type "extension" defined.');
        }

        return $package;
    }
}
