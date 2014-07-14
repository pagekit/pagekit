<?php

namespace Pagekit\Extension\Event;

use Pagekit\Extension\Extension;
use Pagekit\Framework\Event\Event;

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
    public function __construct(Extension $extension, array $config = [])
    {
        $this->extension = $extension;
        $this->config    = $config;
    }

    /**
     * @return Extension
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }
}
