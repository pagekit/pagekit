<?php

namespace Pagekit\Config\Loader;

class PhpLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load($filename)
    {
        return (!($config = require $filename) || 1 === $config) ? [] : $config;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($filename)
    {
        return (bool) preg_match('/\.php$/', $filename);
    }
}
