<?php

namespace Pagekit\Updater;

use Composer\Package\Loader\ArrayLoader;

class ComposerEvent
{
    const CONFIG_FILE = 'packages.json';

    public static function extend($event)
    {
        global $pagekit_packages;

        if (isset($pagekit_packages) && $pagekit_packages) {
            $packages = $pagekit_packages;
        } else if (file_exists(self::CONFIG_FILE)) {
            $packages = json_decode(file_get_contents(self::CONFIG_FILE), true);
        } else {
            return;
        }

        $arrayLoader = new ArrayLoader();
        $composer = $event->getComposer();
        $links = $arrayLoader->parseLinks('pagekit/packages', '1.0.0', 'requires', $packages);
        $requires = $composer->getPackage()->getRequires();
        $composer->getPackage()->setRequires(array_merge($requires, $links));
    }
}