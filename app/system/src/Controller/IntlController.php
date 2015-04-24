<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;

/**
 * TODO should this be available in the frontend as well?
 * @Access(admin=true)
 */
class IntlController
{
    /**
     * @Request({"locale"})
     */
    public function indexAction($locale = null)
    {
        $locale  = substr($locale, 0, 2);
        $numbers = App::intl()->get('numbers', $locale);

        $messages = json_encode([

            'locale' => $locale,

            'main' => [
                $locale => [
                    'dates' => [
                        'calendars' => [
                            'gregorian' => App::intl()->get('calendar', $locale)
                        ]
                    ],
                    'numbers' => array_merge([
                        'defaultNumberingSystem' => 'latn',
                        'symbols-numberSystem-latn' => $numbers['symbols']
                    ])
                ]
            ],

            'supplemental' => ['likelySubtags' => [$locale => App::intl()->guessFullLocale($locale)]],
            'translations' => [$locale => App::translator()->getCatalogue($locale)->all()]

        ]);


        $request = App::request();

        $json = $request->isXmlHttpRequest();

        $response = ($json ? App::response()->json() : App::response('', 200, ['Content-Type' =>'application/javascript']));
        $response->setETag(md5($json.$messages))->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        return $response->setContent($json ? $messages : sprintf('var $globalize = %s;', $messages));
    }
}
