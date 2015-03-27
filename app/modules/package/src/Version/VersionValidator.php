<?php

namespace Pagekit\Package\Version;

class VersionValidator
{
    /**
     * Validates a version
     *
     * @param  string $version
     * @return bool
     */
    public static function validate($version)
    {
        return preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}(-(pre|beta|b|RC|alpha|a|pl|p)([\.]?(\d{1,3}))?)?$/', $version);
    }
}
