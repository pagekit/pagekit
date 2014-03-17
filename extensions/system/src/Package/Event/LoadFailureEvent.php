<?php

namespace Pagekit\Package\Event;

use Symfony\Component\EventDispatcher\Event;

class LoadFailureEvent extends Event
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the extension name that failed to load.
     *
     * @return string
     */
    public function getExtensionName()
    {
        return $this->name;
    }
}
