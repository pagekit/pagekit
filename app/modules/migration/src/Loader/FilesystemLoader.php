<?php

namespace Pagekit\Migration\Loader;

class FilesystemLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load($path, $pattern, $parameters = array())
    {
        $versions = [];

        if (!is_dir($path)) {
            throw new \InvalidArgumentException(sprintf('Unable to run migrations. Could not find path "%s"', $path));
        }

        foreach (new \DirectoryIterator($path) as $file) {
            if ($file->isFile() && preg_match($pattern, $file->getFilename(), $matches) && $config = $this->getConfig($file->getPathname(), $parameters)) {
                $versions[$matches['version']] = $config;
            }
        }

        return $versions;
    }

    protected function getConfig($filename, $parameters)
    {
        extract($parameters, EXTR_SKIP);
        return (!($config = require $filename) || 1 === $config) ? [] : $config;
    }
}
