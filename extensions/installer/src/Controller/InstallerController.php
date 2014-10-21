<?php

namespace Pagekit\Installer\Controller;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Pagekit\Component\Config\Config;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\User\Model\RoleInterface;

/**
 * @Route("/installer")
 */
class InstallerController extends Controller
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
     * @Response("extension://installer/views/install.razr")
     */
    public function indexAction()
    {
        $redirect = $this['router']->getContext()->getBaseUrl() . '/admin';
        return ['head.title' => __('Pagekit Installer'), 'config' => (int) $this->config, 'redirect' => $redirect];
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
                        $this['config']->set($key, $value);
                    }
                }

                $this['db']->connect();

                if ($this['db']->getUtility()->tableExists('@system_option')) {
                    $status = 'tables-exist';
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

            foreach (['blog', 'page', 'system'] as $extension) {
                $this['extensions']->load($extension);
            }

            if ('no-connection' == $status) {
                throw new Exception(__('No database connection.'));
            }

            if ('tables-exist' == $status) {
                throw new Exception($message);
            } else {

                $this['option']->set('system:version', $this['migrator']->create('extension://system/migrations')->run());
                $this['option']->set('system:extensions', ['blog', 'page'], true);

                $this['db']->insert('@system_user', [
                    'name' => $user['username'],
                    'username' => $user['username'],
                    'password' => $this['auth.password']->hash($user['password']),
                    'status' => 1,
                    'email' => $user['email'],
                    'registered' => new \DateTime
                ], ['string', 'string', 'string', 'string', 'string', 'datetime']);

                $id = $this['db']->lastInsertId();

                $this['db']->insert('@system_user_role', [
                    'user_id' => $id,
                    'role_id' => RoleInterface::ROLE_AUTHENTICATED
                ]);

                $this['db']->insert('@system_user_role', [
                    'user_id' => $id,
                    'role_id' => RoleInterface::ROLE_ADMINISTRATOR
                ]);

                $this['extensions']->get('system')->enable();

                // sample data
                $sql = file_get_contents('extension://installer/sample_data.sql');

                foreach (explode(';', $sql) as $query) {
                    if ($query = trim($query)) {
                        $this['db']->executeUpdate($query);
                    }
                }
            }

            if (!$this->config) {

                $configuration = new Config;

                foreach ($config as $key => $value) {
                    $configuration->set($key, $value);
                }

                $configuration->set('app.key', $this['auth.random']->generateString(64));

                if (!file_put_contents($this->configFile, $configuration->dump())) {

                    $status = 'write-failed';

                    throw new Exception(__('Can\'t write config.'));
                }
            }

            foreach ($option as $key => $value) {
                $this['option']->set($key, $value, true);
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
        $params = $this['config']['database.connections'][$this['config']['database.default']];
        $dbname = $this['db']->quoteIdentifier($params['dbname']);

        unset($params['dbname']);

        $db = DriverManager::getConnection($params);
        $db->getSchemaManager()->createDatabase($dbname);
        $db->close();
    }
}
