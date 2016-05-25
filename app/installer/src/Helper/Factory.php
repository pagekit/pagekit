<?php

namespace Pagekit\Installer\Helper;

use Composer\Config;
use Composer\Factory as BaseFactory;
use Composer\IO\IOInterface;

class Factory extends BaseFactory
{
    protected static $config = [];

    public static function bootstrap($config)
    {
        self::$config = $config;
    }

    public static function createConfig(IOInterface $io = null, $cwd = null)
    {
        $config = new Config(true, $cwd);
        $config->merge(['config' => static::$config]);

        return $config;
    }
}
