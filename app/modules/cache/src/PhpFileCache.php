<?php

namespace Pagekit\Cache;

use Doctrine\Common\Cache\PhpFileCache as BasePhpFileCache;

class PhpFileCache extends BasePhpFileCache
{
    /**
     * {@inheritdoc}
     */
    protected $extension = '.cache';

    /**
     * {@inheritdoc}
     */
    protected function getFilename($id)
    {
        return $this->directory . DIRECTORY_SEPARATOR . sha1($id) . $this->extension;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        $file = $this->getFilename($id);

        return @unlink($file);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        foreach (glob($this->directory . DIRECTORY_SEPARATOR . '*' . $this->extension) as $file) {
            @unlink($file);
        }

        return true;
    }
}
