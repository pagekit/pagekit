<?php

namespace Pagekit\Installer\Controller;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\ConnectionException;
use Pagekit\Application as App;
use Pagekit\Config\Config;
use Pagekit\Installer\Package\PackageManager;
use Pagekit\Installer\Package\PackageScripts;
use Pagekit\Util\Arr;
use Symfony\Component\Console\Output\NullOutput;

class InstallerController
{
    /**
     * @var bool
     */
    protected $config;

    /**
     * @var string
     */
    protected $configFile = 'config.php';

    /**
     * @var array
     */
    protected $packages = [
        'pagekit/blog' => '0.10.*',
        'pagekit/theme-one' => '0.10.*'
    ];

    /**
     * Constructor.
     */
    public function __construct()
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        $this->config = file_exists($this->configFile);
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
                'locales' => $intl->getAvailableLanguages()
            ]
        ];
    }

    /**
     * @Request({"config": "array"})
     */
    public function checkAction($config = [])
    {
        $status = 'no-connection';
        $message = '';

        try {

            try {

                if (!$this->config) {
                    foreach ($config as $name => $values) {
                        if ($module = App::module($name)) {
                            $module->config = Arr::merge($module->config, $values);
                        }
                    }
                }

                App::db()->connect();

                if (App::db()->getUtility()->tableExists('@system_config')) {
                    $status = 'tables-exist';
                    $message = __('Existing Pagekit installation detected. Choose different table prefix?');
                } else {
                    $status = 'no-tables';
                }

            } catch (ConnectionException $e) {

                if ($e->getPrevious()->getCode() == 1049) {
                    $this->createDatabase();
                    $status = 'no-tables';
                } else {
                    throw $e;
                }
            }

        } catch (\Exception $e) {

            $message = __('Database connection failed!');

            if ($e->getCode() == 1045) {
                $message = __('Database access denied!');
            }
        }

        return ['status' => $status, 'message' => $message];
    }

    /**
     * @Request({"config": "array", "option": "array", "user": "array"})
     */
    public function installAction($config = [], $option = [], $user = [])
    {
        $status = $this->checkAction($config);
        $message = $status['message'];
        $status = $status['status'];

        try {

            if ('no-connection' == $status) {
                App::abort(400, __('No database connection.'));
            }

            if ('tables-exist' == $status) {
                App::abort(400, $message);
            }

            $scripts = new PackageScripts(App::path().'/app/system/scripts.php');
            $scripts->install();

            App::db()->insert('@system_user', [
                'name' => $user['username'],
                'username' => $user['username'],
                'password' => App::get('auth.password')->hash($user['password']),
                'status' => 1,
                'email' => $user['email'],
                'registered' => date('Y-m-d H:i:s'),
                'roles' => '2,3'
            ]);

            $option['system']['version'] = App::version();
            $option['system']['extensions'] = ['blog'];
            $option['system']['site']['theme'] = 'theme-one';

            foreach ($option as $name => $values) {
                App::config()->set($name, App::config($name)->merge($values));
            }

            if ($this->packages) {
                $installer = new PackageManager(new NullOutput());
                $installer->install($this->packages);
            }

            if (file_exists(__DIR__.'/../../install.php')) {
                require_once __DIR__.'/../../install.php';
            }

            if (!$this->config) {

                $configuration = new Config();
                $configuration->set('application.debug', false);

                foreach ($config as $key => $value) {
                    $configuration->set($key, $value);
                }

                $configuration->set('system.secret', App::get('auth.random')->generateString(64));

                if (!file_put_contents($this->configFile, $configuration->dump())) {

                    $status = 'write-failed';

                    App::abort(400, __('Can\'t write config.'));
                }
            }

            App::module('system/cache')->clearCache();

            $status = 'success';

        } catch (DBALException $e) {

            $status = 'db-sql-failed';
            $message = __('Database error: %error%', ['%error%' => $e->getMessage()]);

        } catch (\Exception $e) {

            $message = $e->getMessage();

        }

        return ['status' => $status, 'message' => $message];
    }

    /**
     * @return void
     */
    protected function createDatabase()
    {
        $module = App::module('database');
        $params = $module->config('connections')[$module->config('default')];

        $name = $params['dbname'];
        unset($params['dbname']);

        $db = DriverManager::getConnection($params);
        $db->getSchemaManager()->createDatabase($db->quoteIdentifier($name));
        $db->close();
    }
}
