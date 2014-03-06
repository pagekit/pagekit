<?php

namespace Pagekit\System\DataCollector;

use Pagekit\System\Helper\SystemInfoHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class SystemDataCollector extends DataCollector
{
    /**
     * @var SystemInfoHelper
     */
    protected $info;

    /**
     * Constructor.
     *
     * @param SystemInfoHelper $info
     */
    function __construct(SystemInfoHelper $info)
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
