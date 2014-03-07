<?php

namespace Pagekit\Installer\Controller;

use Doctrine\DBAL\DBALException;
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
     * @View("installer/install.razr.php")
     */
    public function indexAction()
    {
        return array('head.title' => __('Pagekit Installer'), 'config' => (int) $this->config);
    }

    /**
     * @Request({"config": "array"})
     */
    public function checkAction($config = array(), $response = true)
    {
        try {

            if (!$this->config) {
                foreach ($config as $key => $value) {
                    $this('config')->set($key, $value);
                }
            }

            $this('db')->connect();

            $status  = $this('db')->getUtility()->tableExists('@system_option') ? 'tables-exist' : 'no-tables';
            $message = '';

        } catch (\Exception $e) {

            $status  = 'no-connection';
            $message = __('Database connection failed!');

        }

        return $response ? $this('response')->json(compact('status', 'message')) : $status;
    }

    /**
     * @Request({"config": "array", "option": "array", "user": "array"})
     */
    public function installAction($config = array(), $option = array(), $user = array())
    {
        $status  = $this->checkAction($config, false);
        $message = '';

        try {

            if ('no-connection' == $status) {
                throw new Exception(__('No database connection.'));
            }

            if ('tables-exist' == $status) {

                $this('option')->set('system:version', $this('migrator')->run('extension://system/migrations', $this('option')->get('system:version')));

            } else {

                $this('option')->set('system:version', $this('migrator')->run('extension://system/migrations'));

                $this('db')->insert('@system_user', array(
                    'name' => $user['username'],
                    'username' => $user['username'],
                    'password' => $this('auth.encoder.native')->hash($user['password']),
                    'status' => 1,
                    'email' => $user['email'],
                    'registered' => new \DateTime
                ), array('string', 'string', 'string', 'string', 'string', 'datetime'));

                $id = $this('db')->lastInsertId();

                $this('db')->insert('@system_user_role', array(
                    'user_id' => $id,
                    'role_id' => RoleInterface::ROLE_AUTHENTICATED
                ));

                $this('db')->insert('@system_user_role', array(
                    'user_id' => $id,
                    'role_id' => RoleInterface::ROLE_ADMINISTRATOR
                ));

                // Insert sample data
                $sql = file_get_contents(__DIR__.'/../../sample_data.sql');

                foreach (explode(';', $sql) as $query) {
                    if ($query = trim($query)) {
                        $this('db')->executeUpdate(trim($query));
                    }
                }
            }

            if (!$this->config) {

                $configuration = new Config;

                foreach ($config as $key => $value) {
                    $configuration->set($key, $value);
                }

                $configuration->set('app.key', sha1(uniqid(microtime())));

                if (!file_put_contents($this->configFile, $configuration->dump())) {

                    $status = 'write-failed';

                    throw new Exception(__('Can\'t write config.'));
                }
            }

            foreach ($option as $key => $value) {
                $this('option')->set($key, $value);
            }

            $status = 'success';

        } catch (DBALException $e) {

            $status  = 'db-sql-failed';
            $message = __('Database error: %error%', array('%error%' => $e->getMessage()));

        } catch (\Exception $e) {

            $message = $e->getMessage();
        }

        return $this('response')->json(compact('status', 'message'));
    }
}
