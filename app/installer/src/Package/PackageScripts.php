<?php

namespace Pagekit\Installer\Package;

use Pagekit\Application as App;

class PackageScripts
{
    /**
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $current;

    /**
     * Constructor.
     *
     * @param string $file
     * @param string $current
     */
    public function __construct($file, $current = null)
    {
        $this->file = $file;
        $this->current = $current;
    }

    /**
     * Runs the script's install hook.
     */
    public function install()
    {
        $this->run($this->get('install'));
    }

    /**
     * Runs the script's uninstall hook.
     */
    public function uninstall()
    {
        $this->run($this->get('uninstall'));
    }

    /**
     * Runs the script's enable hook.
     */
    public function enable()
    {
        $this->run($this->get('enable'));
    }

    /**
     * Runs the script's disable hook.
     */
    public function disable()
    {
        $this->run($this->get('disable'));
    }

    /**
     * Runs the script's update hooks.
     */
    public function update()
    {
        $this->run($this->getUpdates());
    }

    /**
     * Checks for script updates.
     */
    public function hasUpdates()
    {
        return (bool) $this->getUpdates();
    }

    /**
     * @param  string $name
     * @return array
     */
    protected function get($name)
    {
        $scripts = $this->load();

        return isset($scripts[$name]) ? $scripts[$name] : [];
    }

    /**
     * @return array
     */
    protected function load()
    {
        if (!$this->file || !file_exists($this->file)) {
            return [];
        }

        return require $this->file;
    }

    /**
     * @param array|callable $scripts
     */
    protected function run($scripts)
    {
        array_map(function ($script) {

            if (is_callable($script)) {
                call_user_func($script, App::getInstance());
            }

        }, (array) $scripts);
    }

    /**
     * @return callable[]
     */
    protected function getUpdates()
    {
        $updates = $this->get('updates');

        $versions = array_filter(array_keys($updates), function ($version) {
            return version_compare($version, $this->current, '>');
        });

        $updates = array_intersect_key($updates, array_flip($versions));
        uksort($updates, 'version_compare');

        return $updates;
    }
}
