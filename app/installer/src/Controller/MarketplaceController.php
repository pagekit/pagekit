<?php

namespace Pagekit\Installer\Controller;

use Pagekit\Application as App;

/**
 * @Access("system: manage packages", admin=true)
 */
class MarketplaceController
{

    /**
     * @Request({"page":"int"})
     */
    public function themesAction($page = null)
    {
        return [
            '$view' => [
                'title' => __('Marketplace'),
                'name'  => 'installer:views/marketplace.php'
            ],
            '$data' => [
                'title' => 'Themes',
                'type' => 'pagekit-theme',
                'api' => App::get('system.api'),
                'installed' => array_values(App::package()->all('pagekit-theme')),
                'page' => $page
            ]
        ];
    }

    /**
     * @Request({"page":"int"})
     */
    public function extensionsAction($page = null)
    {
        return [
            '$view' => [
                'title' => __('Marketplace'),
                'name'  => 'installer:views/marketplace.php'
            ],
            '$data' => [
                'title' => 'Extensions',
                'type' => 'pagekit-extension',
                'api' => App::get('system.api'),
                'installed' => array_values(App::package()->all('pagekit-extension')),
                'page' => $page
            ]
        ];
    }
}
