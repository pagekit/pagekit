<?php

namespace Pagekit\Locale\Controller;

use Pagekit\Application as App;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Access(admin=true)
 */
class LocaleController
{
    /**
     * @Request({"locale"})
     */
    public function indexAction($locale = null)
    {
        $messages = json_encode([
            'translations' => [$locale => App::translator()->getCatalogue($locale)->all()],
            'formats' => App::module('system/locale')->config('formats')
        ]);

        $response = new Response;
        $response->headers->set('Content-Type', 'application/javascript');
        $response->setETag(md5($messages));
        $response->setPublic();

        if ($response->isNotModified(App::request())) {
            return $response;
        }

        $response->setContent(sprintf('var locale = %s;', $messages));

        return $response;
    }
}
