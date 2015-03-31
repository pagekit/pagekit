<?php

namespace Pagekit\View\Asset;

class FileAsset extends Asset
{
    /**
     * {@inheritdoc}
     */
    public function __construct($name, $source, array $dependencies = [], array $options = [])
    {
        if (!isset($options['path']) && file_exists($source)) {
            $options['path'] = $source;
        }

        parent::__construct($name, $source, $dependencies, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        if ($this->content === null and $path = $this->getOption('path')) {
            $this->content = file_get_contents($path);
        }

        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function hash($salt = '')
    {
        $time = '';

        if ($path = $this->getOption('path')) {
            $time = filemtime($path);
        }

        return hash('crc32b', $this->source.$time.$salt);
    }
}
