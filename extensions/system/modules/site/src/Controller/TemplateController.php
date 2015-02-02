<?php

namespace Pagekit\Site\Controller;

use Pagekit\Application as App;

/**
 * @Access(admin=true)
 */
class TemplateController
{
    /**
     * @Request({"name"})
     * @Response(layout=false)
     */
    public function indexAction($name = '')
    {
        $response = App::router()->call('@system/system/tmpl', ['templates' => $name]);

        $templates = json_decode($response->getContent(), true);

        return $templates ? array_pop($templates) : '';
    }
}
