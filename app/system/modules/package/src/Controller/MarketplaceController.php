<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;

/**
 * @Access("system: manage packages", admin=true)
 */
class MarketplaceController
{
    public function themesAction()
    {
        return [
            '$view' => [
                'title' => __('Marketplace'),
                'name'  => 'system/package:views/marketplace.php'
            ],
            '$data' => [
                'title' => 'Themes',
                'type' => 'theme',
                'api' => App::module('system/package')->config('api'),
                'packages' => App::package()->all('pagekit-theme')
            ]
        ];
    }

    public function extensionsAction()
    {
        return [
            '$view' => [
                'title' => __('Marketplace'),
                'name'  => 'system/package:views/marketplace.php'
            ],
            '$data' => [
                'title' => 'Extensions',
                'type' => 'extension',
                'api' => App::module('system/package')->config('api'),
                'packages' => App::package()->all('pagekit-extension')
            ]
        ];
    }
}
