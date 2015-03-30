<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;

/**
 * @Access("system: manage extensions", admin=true)
 */
class MarketplaceController
{
    /**
     * @Response("system: views/admin/settings/marketplace.razr")
     */
    public function indexAction()
    {
        $packages = [];

        foreach (App::package()->getRepository('extension')->getPackages() as $package) {
            $packages[$package->getName()] = $package->getVersion();
        }

        foreach (App::package()->getRepository('theme')->getPackages() as $package) {
            $packages[$package->getName()] = $package->getVersion();
        }

        return ['head.title' => __('Marketplace'), 'api' => App::system()->config('api.url'), 'key' => App::system()->config('api.key'), 'packages' => json_encode($packages)];
    }
}
