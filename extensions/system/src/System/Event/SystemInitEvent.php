<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\Event;
use Pagekit\Framework\Application;
use Symfony\Component\HttpFoundation\Request;

class SystemInitEvent extends Event
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->app['router']->getRequest();
    }
}
