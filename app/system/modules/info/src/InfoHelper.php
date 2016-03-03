<?php

namespace Pagekit\Info;

use Doctrine\DBAL\Driver\PDOConnection;
use Pagekit\Application as App;
use Symfony\Component\HttpFoundation\ServerBag;

class InfoHelper
{
    /**
     * Method to get the system information
     *
     * @return string[]
     */
    public function get()
    {
        $server = new ServerBag($GLOBALS['_SERVER']);

        $info                  = [];
        $info['php']           = php_uname();

        if ($pdo = App::db()->getWrappedConnection() and $pdo instanceof PDOConnection) {
            $info['dbdriver']  = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
            $info['dbversion'] = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
            $info['dbclient']  = $pdo->getAttribute(\PDO::ATTR_CLIENT_VERSION);
        }

        $info['phpversion']    = phpversion();
        $info['server']        = $server->get('SERVER_SOFTWARE', getenv('SERVER_SOFTWARE'));
        $info['sapi_name']     = php_sapi_name();
        $info['version']       = App::version();
        $info['useragent']     = $server->get('HTTP_USER_AGENT');
        $info['extensions']    = implode(", ", get_loaded_extensions());
        $info['directories']   = $this->getDirectories();

        return $info;
    }

    /**
     * Gets a list of files and directories and their writable status.
     *
     * @return string[]
     */
    protected function getDirectories()
    {
        // -TODO-

        $directories = [
            App::get('path.storage'),
            App::get('path.temp'),
            App::get('path.packages'),
            App::get('config.file')
        ];

        $result = [];

        foreach ($directories as $directory) {

            $result[$this->getRelativePath($directory)] = is_writable($directory);

            if (is_dir($directory)) {
                foreach (App::finder()->depth('< 2')->in($directory)->directories() as $dir) {
                    if (!is_writable($dir->getPathname())) {
                        $result[$this->getRelativePath($dir->getPathname())] = false;
                    }

                }
            }
        }

        return $result;
    }

    /**
     * Returns the path relative to the root.
     *
     * @param  string $path
     * @return string
     */
    protected function getRelativePath($path)
    {
        if (0 === strpos($path, App::path())) {
            $path = ltrim(str_replace('\\', '/', substr($path, strlen(App::path()))), '/');
        }

        return $path;
    }
}
