<?php

namespace Pagekit\Updater;

use Composer\Package\Loader\ArrayLoader;

class ComposerEvent
{
    const CONFIG_FILE = 'packages.json';

    public static function extend($event)
    {
        global $packages;

        if (!$packages && file_exists(self::CONFIG_FILE)) {
            $packages = json_decode(file_get_contents(self::CONFIG_FILE), true);
        }

        if ($packages) {

            $loader = new ArrayLoader();
            $package = $event->getComposer()->getPackage();
            $requires = $loader->parseLinks($package->getName(), $package->getVersion(), 'requires', $packages);

            $package->setRequires(array_merge($package->getRequires(), $requires));
        }
    }
}
