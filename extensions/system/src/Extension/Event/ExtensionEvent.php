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
    protected $parameters;

    /**
     * Constructor.
     *
     * @param Extension $extension
     * @param array     $parameters
     */
    public function __construct(Extension $extension, array $parameters = [])
    {
        $this->extension  = $extension;
        $this->parameters = $parameters;
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
