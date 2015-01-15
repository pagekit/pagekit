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

        foreach (App::extension()->getRepository()->getPackages() as $package) {
            $packages[$package->getName()] = $package->getVersion();
        }

        foreach (App::theme()->getRepository()->getPackages() as $package) {
            $packages[$package->getName()] = $package->getVersion();
        }

        return ['head.title' => __('Marketplace'), 'api' => App::config('api.url'), 'key' => App::option('system:api.key'), 'packages' => json_encode($packages)];
    }
}
