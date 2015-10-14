<?php

namespace Pagekit\Twig;

class TwigCache extends \Twig_Cache_Filesystem
{
    protected $dir;

    /**
     * {@inheritdoc}
     */
    public function __construct($directory, $options = 0)
    {
        $this->dir = $directory;

        parent::__construct($directory, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function generateKey($name, $className)
    {
        return $this->dir.'/'.sha1($className).'.twig.cache';
    }
}
