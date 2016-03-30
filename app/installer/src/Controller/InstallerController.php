<?php

namespace Pagekit\Installer\Controller;

use Pagekit\Application as App;
use Pagekit\Installer\Installer;

class InstallerController
{
    /**
     * @var object
     */
    protected $installer;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $app = App::getInstance();
        $this->installer = new Installer($app);
    }

    public function indexAction()
    {
        $intl = App::module('system/intl');

        return [
            '$view' => [
                'title' => __('Pagekit Installer'),
                'name' => 'app/installer/views/installer.php',
            ],
            '$installer' => [
                'locale' => $intl->getLocale(),
                'locales' => $intl->getAvailableLanguages(),
                'sqlite' => class_exists('SQLite3') || (class_exists('PDO') && in_array('sqlite', \PDO::getAvailableDrivers(), true))
            ]
        ];
    }

    /**
     * @Request({"config": "array"})
     */
    public function checkAction($config = [])
    {
        return $this->installer->check($config);
    }

    /**
     * @Request({"config": "array", "option": "array", "user": "array"})
     */
    public function installAction($config = [], $option = [], $user = [])
    {
        return $this->installer->install($config, $option, $user);
    }

}
