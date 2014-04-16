<?php

namespace Pagekit\Theme\Event;

use Pagekit\Framework\Event\Event;
use Pagekit\Theme\Theme;

class ThemeEvent extends Event
{
    /**
     * @var Theme
     */
    protected $theme;

    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param Theme $theme
     * @param array $config
     */
    public function __construct(Theme $theme, array $config = array())
    {
        $this->theme = $theme;
    }

    /**
     * @return Theme
     */
    public function getTheme()
    {
        return $this->theme;
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
