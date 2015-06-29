<?php

namespace Pagekit\System;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\System\Migration\FilesystemLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\TranslatorInterface;

class SystemModule extends Module
{
    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app['system'] = $this;

        $app->factory('finder', function () {
            return Finder::create();
        });

        $app['module']['auth']->config['rememberme.key'] = $this->config('key');

        $app['db.em']; // -TODO- fix me

        foreach ($this->config['extensions'] as $module) {
            try {
                $app['module']->load($module);
            } catch (\RuntimeException $e) {
                $app['log']->warn("Unable to load extension: $module");
            }
        }

        $theme = $this->config('site.theme');
        $app['theme'] = $app['module']->load($theme)->get($theme);

        $app->extend('migrator', function($migrator) {
            return $migrator->setLoader(new FilesystemLoader());
        });
    }

    /**
     * Gets the system menu.
     *
     * @return array
     */
    public function getMenu()
    {
        static $menu;

        if (!$menu) {

            $menu = new SystemMenu();

            foreach (App::module() as $module) {
                foreach ((array) $module->get('menu') as $id => $item) {
                    $menu->addItem($id, $item);
                }
            }
        }

        return $menu;
    }

    /**
     * Loads language files.
     *
     * @param string     $locale
     * @param TranslatorInterface $translator
     */
    public function loadLocale($locale, TranslatorInterface $translator = null)
    {
        $translator = $translator ?: App::translator();

        foreach (App::module() as $module) {

            $domains = [];
            $files   = glob($module->get('path')."/languages/{$locale}/*") ?: [];

            foreach ($files as $file) {

                $format = substr(strrchr($file, '.'), 1);
                $domain = basename($file, '.'.$format);

                if (in_array($domain, $domains)) {
                    continue;
                }

                $domains[] = $domain;

                $translator->addResource($format, $file, $locale, $domain);
                $translator->addResource($format, $file, substr($locale, 0, 2), $domain);
            }
        }
    }
}
