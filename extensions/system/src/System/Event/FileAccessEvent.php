<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\Event;
use Symfony\Component\Finder\Expression\Expression;

class FileAccessEvent extends Event
{
    /**
     * @var string[]
     */
    protected $writePaths = [];

    /**
     * @var string[]
     */
    protected $readPaths = [];

    /**
     * @var string[]
     */
    protected $notPaths = [];

    /**
     * Adds a rule a path must match
     *
     * You can use patterns (delimited with / sign) or simple strings.
     *
     * $event->path('some/special/dir', 'w')
     * $event->path('/some\/special\/dir/', 'w') // same as above
     *
     * @param  string $pattern A pattern (a regexp or a string)
     * @param  string $mode ('r', 'read', 'w', 'write', '-', 'deny'
     * @return $this
     */
    public function path($pattern, $mode = 'r')
    {
        switch ($mode) {

            case 'r':
            case 'read':
                $this->readPaths[] = $this->toRegex($pattern);
                break;

            case 'w':
            case 'write':
                $this->writePaths[] = $this->toRegex($pattern);
                break;

            case '-':
            case 'deny':
                $this->notPaths[] = $this->toRegex($pattern);
                break;
        }

        return $this;
    }

    public function mode($path)
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $path = strtr($path, '\\', '/');
        }

        foreach ($this->notPaths as $regex) {
            if (preg_match($regex, $path)) {
                return '-';
            }
        }

        foreach ($this->writePaths as $regex) {
            if (preg_match($regex, $path)) {
                return 'w';
            }
        }

        foreach ($this->readPaths as $regex) {
            if (preg_match($regex, $path)) {
                return 'r';
            }
        }

        return '-';
    }

    /**
     * Checks whether the string is a regex.
     *
     * @param string $str
     *
     * @return bool    Whether the given string is a regex
     */
    protected function isRegex($str)
    {
        return Expression::create($str)->isRegex();
    }

    /**
     * Converts strings to regexp.
     *
     * PCRE patterns are left unchanged.
     *
     * Default conversion:
     *     'lorem/ipsum/dolor'  ==>  'lorem\/ipsum\/dolor/'
     *
     * Use only / as directory separator (on Windows also).
     *
     * @param string $str Pattern: regexp or dirname.
     *
     * @return string regexp corresponding to a given string or regexp
     */
    protected function toRegex($str)
    {
        return $this->isRegex($str) ? $str : '~'.preg_quote($str, '~').'~';
    }
}
