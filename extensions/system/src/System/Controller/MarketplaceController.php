<?php

namespace Pagekit\System\Controller;

use Pagekit\Framework\Controller\Controller;

/**
 * @Access("system: manage extensions", admin=true)
 */
class MarketplaceController extends Controller
{
    /**
     * @View("system/admin/settings/marketplace.razr")
     */
    public function indexAction()
    {
        $packages = array();

        foreach ($this['extensions']->getRepository()->getPackages() as $package) {
            $packages[$package->getName()] = $package->getVersion();
        }

        foreach ($this['themes']->getRepository()->getPackages() as $package) {
            $packages[$package->getName()] = $package->getVersion();
        }

        return array('head.title' => __('Marketplace'), 'api' => $this['config']->get('api.url'), 'key' => $this['option']->get('system:api.key'), 'packages' => json_encode($packages));
    }
}
