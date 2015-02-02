<?php

namespace Pagekit\Site\Event;

use Symfony\Component\EventDispatcher\Event;

class ConfigEvent extends Event
{
    protected $config;

    public function __construct(array $config)
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

    /**
     * @param array $config
     */
    public function addConfig(array $config)
    {
        $this->config = array_merge_recursive($this->config, $config);
    }
}
