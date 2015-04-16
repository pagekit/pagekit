<?php

namespace Pagekit\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext as BaseContext;

class RequestContext extends BaseContext
{
    /**
     * {@inheritdoc}
     */
    public function fromRequest(Request $request)
    {
        parent::fromRequest($request);

        $this->setBaseUrl($request->server->get('HTTP_MOD_REWRITE') == 'On' ? $request->getBasePath() : "{$request->getBasePath()}/index.php");

        return $this;
    }
}
