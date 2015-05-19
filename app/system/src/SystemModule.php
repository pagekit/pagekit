<?php

namespace Pagekit\System;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Symfony\Component\Finder\Finder;

class SystemModule extends Module
{
    protected $_menu = [];

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

        $this->config['storage'] = '/'.trim(($this->config['storage'] ?: 'storage'), '/');
        $app['path.storage'] = rtrim($app['path'].$this->config['storage'], '/');

        $app['db.em']; // -TODO- fix me

        // TODO access "view" to early
        $app['view']->on('messages', function ($event) use ($app) {

            $result = '';

            if ($app['message']->peekAll()) {
                foreach ($app['message']->levels() as $level) {
                    if ($messages = $app['message']->get($level)) {
                        foreach ($messages as $message) {
                            $result .= sprintf('<div class="uk-alert uk-alert-%1$s" data-status="%1$s">%2$s</div>', $level == 'error' ? 'danger' : $level, $message);
                        }
                    }
                }
            }

            if ($result) {
                $event->setResult(sprintf('<div class="pk-system-messages">%s</div>', $result));
            }

        });

        foreach ($this->config['extensions'] as $module) {
            try {
                $app['module']->load($module);
            } catch (\RuntimeException $e) {
                $app['log']->warn("Unable to load extension: $module");
            }
        }

        $app['module']->load($theme = $this->config['site.theme']);

        if ($app['theme.site'] = $app['module']->get($theme)) {
            $app->on('app.site', function () use ($app) {
                $app['view']->map('layout', $app['theme.site']->getLayout());
            });
        }
    }

    /**
     * Gets the menu.
     *
     * @return array
     */
    public function getMenu()
    {
        if (!$this->_menu) {

            foreach (App::module() as $module) {

                if (!isset($module->menu)) {
                    continue;
                }

                foreach ($module->menu as $id => $item) {
                    $this->registerMenu($id, $item);
                }
            }

            foreach ($this->_menu as $item) {

                if ($item['active'] !== true) {
                    continue;
                }

                while (isset($item['parent'], $this->_menu[$item['parent']])) {
                    $this->_menu[$item['parent']]['active'] = true;
                    $item = $this->_menu[$item['parent']];
                }
            }
        }

        return $this->_menu;
    }

    /**
     * Registers a menu item.
     *
     * @param string $id
     * @param array  $item
     */
    public function registerMenu($id, array $item)
    {
        $meta  = App::user()->get('admin.menu', []);
        $route = App::request()->attributes->get('_route');

        if (isset($item['access']) && !App::user()->hasAccess($item['access'])) {
            return;
        }

        if (isset($meta[$id])) {
            $item['priority'] = $meta[$id];
        }

        if (!isset($item['priority'])) {
            $item['priority'] = 100;
        }

        if (!isset($item['active'])) {
            $item['active'] = $item['url'];
        }

        if (isset($item['active'])) {
            $item['active'] = (bool) preg_match('#^'.str_replace('*', '.*', $item['active']).'$#', $route);
        }

        if (isset($item['url'])) {
            $item['url'] = App::url($item['url']);
        }

        if (isset($item['icon'])) {
            $item['icon'] = App::url()->getStatic($item['icon']);
        }

        $this->_menu[$id] = array_replace(['id' => $id, 'label' => $id, 'parent' => 'root'], $item);
    }
}
