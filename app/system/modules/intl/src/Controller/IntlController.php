<?php

namespace Pagekit\Intl\Controller;

use Pagekit\Application as App;

class IntlController
{
    /**
     * TODO: Limit catalogue if maintenance mode is enabled?
     * @Route("/{locale}", requirements={"locale"="[a-zA-Z0-9_-]+"}, defaults={"_maintenance" = true})
     * @Request({"locale"})
     */
    public function indexAction($locale = null)
    {
        $intl = App::module('system/intl');
        $intl->loadLocale($locale);

        $messages = $intl->getFormats($locale) ?: [];
        $messages['locale'] = $locale;
        $messages['translations'] = [$locale => App::translator()->getCatalogue($locale)->all()];
        $messages = json_encode($messages);

        $request = App::request();

        $json = $request->isXmlHttpRequest();

        $response = ($json ? App::response()->json() : App::response('', 200, ['Content-Type' => 'application/javascript']));
        $response->setETag(md5($json . $messages))->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        return $response->setContent($json ? $messages : sprintf('var $locale = %s;', $messages));
    }
}
