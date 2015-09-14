<?php

namespace Pagekit\Installer\Helper;

use Composer\Factory as BaseFactory;


class Factory extends BaseFactory
{

    protected static $homeDir;

    public static function setHomeDir($homeDir)
    {
        self::$homeDir = $homeDir;
    }

    protected static function getHomeDir()
    {
        if (static::$homeDir) {
            return static::$homeDir;
        }

        return parent::getHomeDir();
    }

}
