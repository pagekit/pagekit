<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Exception;

/**
 * @Access("system: manage themes", admin=true)
 */
class ThemesController
{
    public function indexAction()
    {
        $packages = App::package()->getRepository('theme')->getPackages();

        foreach ($packages as $package) {
            $package->enabled = App::system()->config('theme.site') == $package->getName();
        }

        // uasort($packages, function($themeA, $themeB) use ($current) {
        //     if ($current === $themeA) {
        //         return -1;
        //     } elseif ($current === $themeB) {
        //         return 1;
        //     }

        //     return strcmp($themeA->getName(), $themeB->getName());
        // });

        return [
            '$view' => [
                'title' => __('Themes'),
                'name'  => 'system:modules/package/views/themes.php'
            ],
            '$themes' => [
                'api' => App::system()->config('api'),
                'packages' => $packages
            ]
        ];
    }

    /**
     * @Request({"name"}, csrf=true)
     */
    public function enableAction($name)
    {
        try {

            ini_set('display_errors', 0);

            $handler = App::exception()->setHandler(function($exception) {

                while (ob_get_level()) {
                    ob_get_clean();
                }

                App::response()->json(['error' => true, 'message' => __('Unable to activate theme.<br>The theme triggered a fatal error.')])->send();
            });

            App::module()->load($name);

            if (!$theme = App::module($name)) {
                throw new Exception(__('Unable to enable theme "%name%".', ['%name%' => $name]));
            }

            App::config('system')->set('theme.site', $theme->name);
            App::exception()->setHandler($handler);

            return ['message' => __('Theme enabled.')];

        } catch (Exception $e) {

            return ['message' => $e->getMessage(), 'error' => true];
        }
    }

    /**
     * @Request({"name"}, csrf=true)
     */
    public function uninstallAction($name)
    {
        try {

            if (!$theme = App::package()->getRepository('theme')->findPackage($name)) {
                throw new Exception(__('Unable to uninstall theme "%name%".', ['%name%' => $name]));
            }

            App::package()->getInstaller('theme')->uninstall($theme);
            App::module('system/cache')->clearCache();

            return ['message' => __('Theme uninstalled.')];

        } catch (Exception $e) {

            return ['message' => $e->getMessage(), 'error' => true];
        }
    }
}
