<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;

class IntlController
{
    const CACHE_KEY = 'intl.locations:';

    /**
     * TODO: does this need a permission?
     * @Request({"locale"})
     */
    public function indexAction($locale = null)
    {
        App::system()->loadLocale($locale);

        $minLocale = substr($locale, 0, stripos($locale, '_'));

        $numbers = App::intl()->get('numbers', $locale);
        $dateFields = App::intl()->get('dateFields', $locale);
        $plurals = json_decode(file_get_contents(App::path() . '/app/modules/intl/data/plurals.json'), true);

        $messages = json_encode([

            'locale' => $minLocale,

            'main' => [
                $minLocale => [
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
                'likelySubtags' => [$minLocale => App::intl()->guessFullLocale($minLocale)],
                'plurals-type-cardinal' => [$minLocale => $plurals['supplemental']['plurals-type-cardinal'][$minLocale]]
            ],

            'translations' => [$minLocale => App::translator()->getCatalogue($locale)->all()]

        ]);

        $request = App::request();

        $json = $request->isXmlHttpRequest();

        $response = ($json ? App::response()->json() : App::response('', 200, ['Content-Type' => 'application/javascript']));
        $response->setETag(md5($json . $messages))->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        return $response->setContent($json ? $messages : sprintf('var $globalize = %s;', $messages));
    }
}
