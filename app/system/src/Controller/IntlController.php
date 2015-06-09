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
        $dateFields = App::intl()->get('dateFields', $locale);
        $plurals = json_decode(file_get_contents(App::path().'/app/modules/intl/data/plurals.json'), true);

        $messages = json_encode([

            'locale' => $locale,

            'main' => [
                $locale => [
                    'dates' => [
                        'calendars' => [
                            'gregorian' => App::intl()->get('calendar', $locale)
                        ],
                        'fields' => $dateFields
                    ],
                    'numbers' => array_merge([
                        'defaultNumberingSystem' => 'latn',
                        'symbols-numberSystem-latn' => $numbers['symbols'],
                        'decimalFormats-numberSystem-latn' => [
                          'standard' => '#,##0.###'
                        ]
                    ])
                ]
            ],

            'supplemental' => [
                'likelySubtags' => [$locale => App::intl()->guessFullLocale($locale)],
                'plurals-type-cardinal' => [$locale => $plurals['supplemental']['plurals-type-cardinal'][$locale]]
            ],
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
