<?php

namespace Pagekit\Filesystem;

class Path
{
    /**
     * Parses and canonicalizes a path into root, path, dirname, pathname, protocol.
     *
     * @param  string $path
     * @param  string $option
     * @return array
     */
    public static function parse($path, $option = null)
    {
        $root = '';
        $path = strtr($path, '\\', '/');

        if (preg_match('@^(?:/|[a-z]:/?|[a-z]+://)@i', $path, $parts)) {
            $root = $parts[0];
            $path = substr($path, strlen($root));
        }

        $parts = [];

        foreach (array_filter(explode('/', $path), 'strlen') as $part) {
            if ('..' == $part) {

                if (count($parts)) {
                    array_pop($parts);
                    continue;
                } elseif (!$root) {
                    continue;
                }

            } elseif ('.' != $part) {
                $parts[] = $part;
            }
        }

        $path = implode('/', $parts);
        $info = compact('root', 'path');

        $info['dirname']  = $root.substr($path, 0, strrpos($path, '/'));
        $info['pathname'] = $root.$path;
        $info['protocol'] = strpos($root, '://') ? substr($root, 0, -3) : 'file';

        if ($option === null) {
            return $info;
        }

        return array_key_exists($option, $info) ? $info[$option] : '';
    }

    /**
     * Returns whether a path is absolute.
     *
     * @param  string $path
     * @return bool
     */
    public static function isAbsolute($path)
    {
        return self::parse($path, 'root') !== '';
    }

    /**
     * Returns whether a path is relative.
     *
     * @param  string $path
     * @return bool
     */
    public static function isRelative($path)
    {
        return self::parse($path, 'root') === '';
    }
}
