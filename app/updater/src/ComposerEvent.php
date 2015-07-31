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
        $packages = file_exists(self::CONFIG_FILE) ? json_decode(file_get_contents(self::CONFIG_FILE), true) : [];

        if ($packages) {
            $links = $arrayLoader->parseLinks('pagekit/packages', '1.0.0', 'requires', $packages);
            $requires = $composer->getPackage()->getRequires();
            $composer->getPackage()->setRequires(array_merge($requires, $links));
        }
    }
}