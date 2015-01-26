<?php

namespace Pagekit\Installer\Controller;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Pagekit\Application as App;
use Pagekit\Application\Exception;
use Pagekit\Config\Config;
use Pagekit\User\Model\RoleInterface;

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
    protected $configFile = 'app://config.php';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->config = file_exists($this->configFile);
    }

    /**
     * @Response("extensions/installer/views/install.razr")
     */
    public function indexAction()
    {
        return ['config' => (int) $this->config, 'redirect' => App::url('/admin')];
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
                    foreach ($config as $key => $value) {
                        App::config()->set($key, $value);
                    }
                }

                App::db()->connect();

                if (App::db()->getUtility()->tableExists('@system_option')) {
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
            } else {
                $message .= " " . __('Message was: ') . $e->getMessage();
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

            foreach (['blog', 'page', 'system'] as $extension) {
                App::extension()->load($extension);
            }

            if ('no-connection' == $status) {
                throw new Exception(__('No database connection.'));
            }

            if ('tables-exist' == $status) {
                throw new Exception($message);
            } else {

                App::option()->set('system:version', App::migrator()->create('extensions/system/migrations')->run());
                App::option()->set('system:extensions', ['blog', 'page'], true);

                App::db()->insert('@system_user', [
                    'name'       => $user['username'],
                    'username'   => $user['username'],
                    'password'   => App::get('auth.password')->hash($user['password']),
                    'status'     => 1,
                    'email'      => $user['email'],
                    'registered' => new \DateTime
                ], ['string', 'string', 'string', 'string', 'string', 'datetime']);

                $id = App::db()->lastInsertId();

                App::db()->insert('@system_user_role', [
                    'user_id' => $id,
                    'role_id' => RoleInterface::ROLE_AUTHENTICATED
                ]);

                App::db()->insert('@system_user_role', [
                    'user_id' => $id,
                    'role_id' => RoleInterface::ROLE_ADMINISTRATOR
                ]);

                App::extension('system')->enable();

                // sample data
                $sql = file_get_contents('extensions/installer/sample_data.sql');

                foreach (explode(';', $sql) as $query) {
                    if ($query = trim($query)) {
                        App::db()->executeUpdate($query);
                    }
                }
            }

            if (!$this->config) {

                $configuration = new Config;

                foreach ($config as $key => $value) {
                    $configuration->set($key, $value);
                }

                $configuration->set('app.key', App::get('auth.random')->generateString(64));

                if (!file_put_contents($this->configFile, $configuration->dump())) {

                    $status = 'write-failed';

                    throw new Exception(__('Can\'t write config.'));
                }
            }

            foreach ($option as $key => $value) {
                App::option()->set($key, $value, true);
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
        $params = App::config('database.connections')[App::config('database.default')];
        $dbname = App::db()->quoteIdentifier($params['dbname']);

        unset($params['dbname']);

        $db = DriverManager::getConnection($params);
        $db->getSchemaManager()->createDatabase($dbname);
        $db->close();
    }
}
