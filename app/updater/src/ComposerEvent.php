<?php

namespace Pagekit\Updater;

use Composer\Package\Loader\ArrayLoader;

class ComposerEvent
{
    const CONFIG_FILE = 'packages.json';

    public static function extend($event)
    {
        $arrayLoader = new ArrayLoader();
        $composer = $event->getComposer();
        $config = file_exists(self::CONFIG_FILE) ? json_decode(file_get_contents(self::CONFIG_FILE), true) : [];

        if (isset($config['require']) && $config['require']) {
            $links = $arrayLoader->parseLinks('pagekit/packages', '1.0.0', 'requires', $config['require']);
            $requires = $composer->getPackage()->getRequires();
            $composer->getPackage()->setRequires(array_merge($requires, $links));
        }

        if (isset($config['packages']) && is_array($config['packages'])) {
            $repoManger = $composer->getRepositoryManager();
            foreach ($config['packages'] as $package) {
                $repoManger->addRepository($repoManger->createRepository('package', $package));
            }
        }
    }
}