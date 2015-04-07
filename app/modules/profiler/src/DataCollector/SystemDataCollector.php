<?php

namespace Pagekit\Profiler\DataCollector;

use Pagekit\System\Info\InfoHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class SystemDataCollector extends DataCollector
{
    /**
     * @var InfoHelper
     */
    protected $info;

    /**
     * Constructor.
     *
     * @param InfoHelper $info
     */
    function __construct(InfoHelper $info)
    {
        $this->info = $info;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = $this->info->get();
    }

    public function getInfo()
    {
        return $this->data;
    }

    public function getName()
    {
        return 'system';
    }
}
