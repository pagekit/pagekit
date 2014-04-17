<?php

namespace Pagekit\Extension\Event;

use Pagekit\Framework\Event\Event;
use Pagekit\Extension\Extension;

class ExtensionEvent extends Event
{
    /**
     * @var Extension
     */
    protected $extension;

    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param Extension $extension
     * @param array     $config
     */
    public function __construct(Extension $extension, array $config = array())
    {
        $this->extension = $extension;
    }

    /**
     * @return Extension
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
