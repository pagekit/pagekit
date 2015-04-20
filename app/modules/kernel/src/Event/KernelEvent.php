<?php

namespace Pagekit\Kernel\Event;

use Pagekit\Event\Event;
use Pagekit\Kernel\HttpKernel;

class KernelEvent extends Event
{
    /**
     * @var int
     */
    protected $requestType;

    /**
     * Constructor.
     *
     * @param string   $name
     * @param int      $requestType
     */
    public function __construct($name, $requestType)
    {
        parent::__construct($name);

        $this->requestType = $requestType;
    }

    /**
     * Gets the request type.
     *
     * @return int
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * Checks if this is a master request.
     *
     * @return bool
     */
    public function isMasterRequest()
    {
        return HttpKernel::MASTER_REQUEST === $this->requestType;
    }
}
