<?php

namespace Pagekit\System\Controller;

use GuzzleHttp\Client;
use Pagekit\Application as App;
use Pagekit\Kernel\Exception\NotFoundException;

/**
 * TODO should this be available in the frontend as well?
 * @Access(admin=true)
 */
class IntlController
{
    const CACHE_KEY = 'intl.locations:';

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

    /**
     * @Request({"lat", "lon"})
     */
    public function timezoneAction($lat, $lon)
    {
        $location = $lat.','.$lon;

        if (!$data = App::cache()->fetch(self::CACHE_KEY.$location)) {

            $client = new Client();

            try {

                $data = @json_decode($client->get('https://maps.googleapis.com/maps/api/timezone/json', [
                    'query' => [
                        'location' => $location,
                        'timestamp' => time()
                    ]
                ])->getBody());

                $data = [
                    'id' => $data->timeZoneId,
                    'name' => $data->timeZoneName,
                    'offset' => $data->rawOffset + $data->dstOffset
                ];

                App::cache()->save(self::CACHE_KEY.$location, $data, 86400);

            } catch (\Exception $e) {
                throw new NotFoundException('No timezone found.');
            }

        }

        return $data;

    }
}
