<?php

namespace Pagekit\Locale\Controller;

use Pagekit\Application as App;

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

        $response = App::response('', 200, ['Content-Type' =>'application/javascript']);
        $response->setETag(md5($messages));
        $response->setPublic();

        if ($response->isNotModified(App::request())) {
            return $response;
        }

        return $response->setContent(sprintf('var locale = %s;', $messages));
    }
}
