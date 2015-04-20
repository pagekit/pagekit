<?php

namespace Pagekit\View\Asset;

class FileAsset extends Asset
{
    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        if ($this->content === null and $path = $this->getPath()) {
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

        if ($path = $this->getPath()) {
            $time = filemtime($path);
        }

        return hash('crc32b', $this->source.$time.$salt);
    }

    /**
     * @return string
     */
    protected function getPath()
    {
        if (!isset($this->options['path']) && file_exists($this->source)) {
            return $this->source;
        }

        return $this->getOption('path');
    }
}
