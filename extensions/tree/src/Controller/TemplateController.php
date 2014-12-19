<?php

namespace Pagekit\Tree\Controller;

use Pagekit\Framework\Controller\Controller;

/**
 * @Access(admin=true)
 */
class TemplateController extends Controller
{
    /**
     * @Request({"name"})
     * @Response(layout=false)
     */
    public function indexAction($name = '')
    {
        $response = $this['router']->call('@system/system/tmpl', ['templates' => $name]);

        $templates = json_decode($response->getContent(), true);

        return $templates ? array_pop($templates) : '';
    }
}
