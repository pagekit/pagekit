<?php

namespace Pagekit\Installer;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Pagekit\Application as App;
use Pagekit\Application\Exception;
use Pagekit\Config\Config;

/**
 * @Route("/installer")
 */
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
     * Constructor.
     */
    public function __construct()
    {
        $this->config = file_exists($this->configFile);
    }

    /**
     * @Response("app/installer/views/install.php")
     */
    public function indexAction()
    {
        return [
            '$meta' => ['title' => 'Pagekit Installer'],
            'redirect' => App::url('/admin')
        ];
    }

    /**
     * @Request({"config": "array"})
     * @Response("json")
     */
    public function checkAction($config = [])
    {
        $status  = 'no-connection';
        $message = '';

        try {

            try {

                if (!$this->config) {
                    foreach ($config as $name => $values) {
                        if ($module = App::module($name)) {
                            $module->config = array_replace_recursive($module->config, $values);
                        }
                    }
                }

                App::db()->connect();

                if (App::db()->getUtility()->tableExists('@system_config')) {
                    $status  = 'tables-exist';
                    $message = __('Existing Pagekit installation detected. Choose different table prefix?');
                } else {
                    $status = 'no-tables';
                }

            } catch (\PDOException $e) {

                if ($e->getCode() == 1049) {
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
     * @Response("json")
     */
    public function installAction($config = [], $option = [], $user = [])
    {
        $status  = $this->checkAction($config, false);
        $message = $status['message'];
        $status  = $status['status'];

        try {

            if ('no-connection' == $status) {
                throw new Exception(__('No database connection.'));
            }

            if ('tables-exist' == $status) {
                throw new Exception($message);
            }

            if ($version = App::migrator()->create('app/system/migrations')->run()) {
                App::config()->set('system', compact('version'));
            }

            App::db()->insert('@system_user', [
                'name'       => $user['username'],
                'username'   => $user['username'],
                'password'   => App::get('auth.password')->hash($user['password']),
                'status'     => 1,
                'email'      => $user['email'],
                'registered' => new \DateTime
            ], ['string', 'string', 'string', 'string', 'string', 'datetime']);

            $id = App::db()->lastInsertId();

            App::db()->insert('@system_user_role', ['user_id' => $id, 'role_id' => 2]);
            App::db()->insert('@system_user_role', ['user_id' => $id, 'role_id' => 3]);

            if ($sampleData = App::module('installer')->config('sampleData')) {

                $sql = file_get_contents($sampleData);

                foreach (explode(';', $sql) as $query) {
                    if ($query = trim($query)) {
                        App::db()->executeUpdate($query);
                    }
                }
            }

            if (!$this->config) {

                $configuration = new Config();

                foreach ($config as $key => $value) {
                    $configuration->set($key, $value);
                }

                $configuration->set('system.key', App::get('auth.random')->generateString(64));

                if (!file_put_contents($this->configFile, $configuration->dump())) {

                    $status = 'write-failed';

                    throw new Exception(__('Can\'t write config.'));
                }
            }

            $status = 'success';

        } catch (DBALException $e) {

            $status  = 'db-sql-failed';
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
        $params = $module->config('database.connections')[$module->config('database.default')];
        $dbname = App::db()->quoteIdentifier($params['dbname']);

        unset($params['dbname']);

        $db = DriverManager::getConnection($params);
        $db->getSchemaManager()->createDatabase($dbname);
        $db->close();
    }
}
