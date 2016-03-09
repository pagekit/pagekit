<?php

namespace Pagekit\Installer;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\ConnectionException;
use Pagekit\Application;
use Pagekit\Config\Config;
use Pagekit\Installer\Package\PackageManager;
use Pagekit\Installer\Package\PackageScripts;
use Pagekit\Util\Arr;
use Symfony\Component\Console\Output\NullOutput;


class Installer
{

    /**
     * @var string
     */
    protected $configFile = 'config.php';


    /**
     * @var Application Pagekit Application instance
     */
    protected $app;

    /**
     * @var bool
     */
    protected $config;

    public function __construct(Application $app)
    {
        $this->app = $app;

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        $this->config = file_exists($this->configFile);
    }

    public function check($config)
    {
        $status = 'no-connection';
        $message = '';

        try {

            try {

                if (!$this->config) {
                    foreach ($config as $name => $values) {
                        if ($module = $this->app->module($name)) {
                            $module->config = Arr::merge($module->config, $values);
                        }
                    }
                }

                $this->app->db()->connect();

                if ($this->app->db()->getUtility()->tableExists('@system_config')) {
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

    public function install($config = [], $option = [], $user = [])
    {
        $status = $this->check($config);
        $message = $status['message'];
        $status = $status['status'];

        try {

            if ('no-connection' == $status) {
                $this->app->abort(400, __('No database connection.'));
            }

            if ('tables-exist' == $status) {
                $this->app->abort(400, $message);
            }

            $scripts = new PackageScripts($this->app->path().'/app/system/scripts.php');
            $scripts->install();

            $this->app->db()->insert('@system_user', [
                'name' => $user['username'],
                'username' => $user['username'],
                'password' => $this->app->get('auth.password')->hash($user['password']),
                'status' => 1,
                'email' => $user['email'],
                'registered' => date('Y-m-d H:i:s'),
                'roles' => '2,3'
            ]);

            $option['system']['version'] = $this->app->version();

            foreach ($option as $name => $values) {
                $this->app->config()->set($name, $this->app->config($name)->merge($values));
            }

            $packageManager = new PackageManager(new NullOutput());
            foreach (glob($this->app->get('path.packages') . '/*/*/composer.json') as $package) {
                $package = $this->app->package()->load($package);
                if ($package->get('type') === 'pagekit-extension' || $package->get('type') === 'pagekit-theme' ) {
                    $packageManager->enable($package);
                }
            }

            if (file_exists(__DIR__.'/../install.php')) {
                require_once __DIR__.'/../install.php';
            }

            if (!$this->config) {

                $configuration = new Config();
                $configuration->set('application.debug', false);

                foreach ($config as $key => $value) {
                    $configuration->set($key, $value);
                }

                $configuration->set('system.secret', $this->app->get('auth.random')->generateString(64));

                if (!file_put_contents($this->configFile, $configuration->dump())) {

                    $status = 'write-failed';

                    $this->app->abort(400, __('Can\'t write config.'));
                }
            }

            $this->app->module('system/cache')->clearCache();

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
        $module = $this->app->module('database');
        $params = $module->config('connections')[$module->config('default')];

        $name = $params['dbname'];
        unset($params['dbname']);

        $db = DriverManager::getConnection($params);
        $db->getSchemaManager()->createDatabase($db->quoteIdentifier($name));
        $db->close();
    }

}