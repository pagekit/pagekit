<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;

class IntlController
{
    /**
     * TODO: does this need a permission?
     * @Route("/{locale}", requirements={"locale"="[a-zA-Z0-9_-]+"})
     * @Request({"locale"})
     */
    public function indexAction($locale = null)
    {
        App::system()->loadLocale($locale);

        $messages = [];

        $file = "vendor/assets/vue-intl/dist/locales/{$locale}.json";
        if (App::file()->exists($file)) {
            $messages = json_decode(file_get_contents(App::file()->getPath($file)), true);
        }

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
