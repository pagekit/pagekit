<?php

namespace Pagekit\View\Asset;

class FileAsset extends Asset
{
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
