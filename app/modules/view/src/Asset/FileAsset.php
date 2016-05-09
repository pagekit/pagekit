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
     * {@inheritdoc}
     */
    public function getPath()
    {
        return file_exists($this->source) ? $this->source : false;
    }

    public function __toString()
    {
        return '';
    }
}
