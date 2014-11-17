<?php

namespace Pagekit\Theme;

use Pagekit\System\Package\PackageManager;

class ThemeManager extends PackageManager
{
    /**
     * {@inheritdoc}
     */
    public function load($name, $path = null)
    {
        $root = $path ?: $this->repository->getPath()."/$name";

        if (strpos($root, '://') > 0 ) {
            $root = $this->app['file']->locate($root);
        }

        if (!is_string($name)) {
            throw new \InvalidArgumentException('Theme name must be of type string.');
        }

        if (isset($this->loaded[$name])) {
            throw new \InvalidArgumentException(sprintf('Theme already loaded %s.', $name));
        }

        if (!file_exists("$root/theme.php")) {
            throw new \InvalidArgumentException('Theme path does not exist.');
        }

        $fn = function($app, $bootstrap) {
            return include $bootstrap;
        };

        $config = (!$config = $fn($this->app, "$root/theme.php") or 1 === $config) ? [] : $config;
        $class  = isset($config['main']) ? $config['main'] : 'Pagekit\Theme\Theme';

        if (isset($config['autoload'])) {
            foreach ($config['autoload'] as $namespace => $path) {
                $this->autoloader->addPsr4($namespace, "$root/$path");
            }
        }

        return $this->loaded[$name] = new $class($name, $root, $config);
    }
}
