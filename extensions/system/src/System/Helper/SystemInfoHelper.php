<?php

namespace Pagekit\System\Helper;

use Doctrine\DBAL\Driver\PDOConnection;
use Pagekit\Framework\ApplicationTrait;
use PDO;

class SystemInfoHelper implements \ArrayAccess
{
    use ApplicationTrait;

    /**
     * Method to get the system information
     *
     * @return string[]
     */
    public function get()
    {
        $server = $this['request']->server;

        $info                  = [];
        $info['php']           = php_uname();

        if ($pdo = $this['db']->getWrappedConnection() and $pdo instanceof PDOConnection) {
            $info['dbdriver']  = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            $info['dbversion'] = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
            $info['dbclient']  = $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION);
        }

        $info['phpversion']    = phpversion();
        $info['server']        = $server->get('SERVER_SOFTWARE', getenv('SERVER_SOFTWARE'));
        $info['sapi_name']     = php_sapi_name();
        $info['version']       = $this['config']->get('app.version');
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
            $this['path.extensions'],
            $this['path.storage'],
            $this['path.themes'],
            $this['config.file'],
            $this['path'].'/app'
        ];

        $result = [];

        foreach ($directories as $directory) {

            $result[$this->getRelativePath($directory)] = is_writable($directory);

            if (is_dir($directory)) {
                foreach ($this['finder']->in($directory)->directories()->depth(0) as $dir) {
                    $result[$this->getRelativePath($dir->getPathname())] = is_writable($dir->getPathname());
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
        if (0 === strpos($path, $this['path'])) {
            $path = ltrim(str_replace('\\', '/', substr($path, strlen($this['path']))), '/');
        }

        return $path;
    }
}
