<?php

namespace Pagekit\Kernel\Event;

use Pagekit\Event\Event;
use Pagekit\Kernel\HttpKernelInterface;

class KernelEvent extends Event
{
    /**
     * @var HttpKernelInterface
     */
    protected $kernel;

    /**
     * Constructor.
     *
     * @param string              $name
     * @param HttpKernelInterface $kernel
     */
    public function __construct($name, HttpKernelInterface $kernel)
    {
        parent::__construct($name);

        $this->kernel = $kernel;
    }

    /**
     * Gets the kernel.
     *
     * @return HttpKernelInterface
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * Checks if this is a master request.
     *
     * @return bool
     */
    public function isMasterRequest()
    {
        return $this->kernel->isMasterRequest();
    }
}
