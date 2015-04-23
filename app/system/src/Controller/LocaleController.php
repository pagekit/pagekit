<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;

/**
 * TODO should this be available in the frontend as well?
 * @Access(admin=true)
 */
class LocaleController
{
    /**
     * @Request({"locale"})
     */
    public function indexAction($locale = null)
    {
        $formats = [];

        foreach (['full', 'long', 'medium', 'short'] as $name) {
            $formats[$name] = App::intl('calendar')->getDateFormat($name);
        }

        $messages = json_encode([
            'translations' => [$locale => App::translator()->getCatalogue($locale)->all()],
            'formats' => $formats
        ]);

        $request = App::request();

        $json = $request->isXmlHttpRequest();

        $response = ($json ? App::response()->json() : App::response('', 200, ['Content-Type' =>'application/javascript']));
        $response->setETag(md5($json.$messages))->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        return $response->setContent($json ? $messages : sprintf('var $locale = %s;', $messages));
    }
}
