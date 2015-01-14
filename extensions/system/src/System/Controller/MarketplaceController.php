<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;

/**
 * @Access("system: manage extensions", admin=true)
 */
class MarketplaceController
{
    /**
     * @Response("extensions/system/views/admin/settings/marketplace.razr")
     */
    public function indexAction()
    {
        $packages = [];

        foreach (App::extensions()->getRepository()->getPackages() as $package) {
            $packages[$package->getName()] = $package->getVersion();
        }

        foreach (App::themes()->getRepository()->getPackages() as $package) {
            $packages[$package->getName()] = $package->getVersion();
        }

        return ['head.title' => __('Marketplace'), 'api' => App::config()->get('api.url'), 'key' => App::option()->get('system:api.key'), 'packages' => json_encode($packages)];
    }
}
