<?php

namespace Pagekit\Module\Loader;

use Composer\Autoload\ClassLoader;

class AutoLoader implements LoaderInterface
{
    /**
     * @var ClassLoader
     */
    protected $loader;

    /**
     * Constructor.
     *
     * @param ClassLoader $loader
     */
    public function __construct(ClassLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function load($module)
    {
        if (isset($module['autoload'])) {
            foreach ($module['autoload'] as $namespace => $path) {
                $this->loader->addPsr4($namespace, $this->resolvePath($module, $path));
            }
        }

        return $module;
    }

    /**
     * Resolves a path to a absolute module path.
     *
     * @param  array  $module
     * @param  string $path
     * @return string
     */
    protected function resolvePath($module, $path)
    {
        $path = strtr($path, '\\', '/');

        if (!($path[0] == '/' || (strlen($path) > 3 && ctype_alpha($path[0]) && $path[1] == ':' && $path[2] == '/'))) {
            $path = $module['path']."/$path";
        }

        return $path;
    }
}
