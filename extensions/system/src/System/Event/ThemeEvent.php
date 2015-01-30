<?php

namespace Pagekit\System\Event;

use Pagekit\System\Theme;
use Symfony\Component\EventDispatcher\Event;

class ThemeEvent extends Event
{
    /**
     * @var Theme
     */
    protected $theme;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Constructor.
     *
     * @param Theme $theme
     * @param array $parameters
     */
    public function __construct(Theme $theme, array $parameters = [])
    {
        $this->theme = $theme;
        $this->parameters = $parameters;
    }

    /**
     * @return Theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParams(array $parameters)
    {
        $this->parameters = $parameters;
    }
}
