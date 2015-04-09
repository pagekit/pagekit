<?php

namespace Pagekit\View\Asset;

class TemplateAsset extends Asset
{
    /**
     * {@inheritdoc}
     */
    public function __construct($name, $source, array $dependencies = array(), array $options = array())
    {
        $options['template'] = $source;

        parent::__construct($name, null, $dependencies, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function hash($salt = '')
    {
        return hash('crc32b', $this->getOption('template').$salt);
    }
}
