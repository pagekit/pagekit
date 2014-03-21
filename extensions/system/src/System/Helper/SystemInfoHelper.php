<?php

namespace Pagekit\System\Helper;

use Doctrine\DBAL\Driver\PDOConnection;
use Pagekit\Component\Routing\Event\GenerateUrlEvent;
use Pagekit\Framework\ApplicationAware;
use PDO;
use Symfony\Component\Finder\Finder;

class SystemInfoHelper extends ApplicationAware
{
    /**
     * Method to get the system information
     *
     * @return string[]
     */
    public function get()
    {
        $server = $this('request')->server;

        $info                  = array();
        $info['php']           = php_uname();

        if ($pdo = $this('db')->getWrappedConnection() and $pdo instanceof PDOConnection) {
            $info['dbdriver']  = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            $info['dbversion'] = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
            $info['dbclient']  = $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION);
        }

        $info['phpversion']    = phpversion();
        $info['server']        = $server->get('SERVER_SOFTWARE', getenv('SERVER_SOFTWARE'));
        $info['sapi_name']     = php_sapi_name();
        $info['version']       = $this('config')->get('app.version');
        $info['useragent']     = $server->get('HTTP_USER_AGENT');
        $info['extensions']    = implode(", ", get_loaded_extensions());
        $info['directories']   = $this->getDirectories();

        return $info;
    }

    /**
     * Resolves an internal url
     *
     * @param  string $url
     * @return bool|string
     */
    public function resolveUrl($url)
    {
        $listener = function(GenerateUrlEvent $event) { $event->stopPropagation(); };
        $this('events')->on('url.generate', $listener, 32);

        try {
            $url = $this('url')->route($url, array(), 'base');
        } catch (\Exception $e) {
            $url = false;
        }

        $this('events')->removeListener('url.generate', $listener);

        return $url;
    }

    /**
     * Gets a list of files and directories and their writable status.
     *
     * @return string[]
     */
    protected function getDirectories()
    {
        $directories = array(
            'extension://',
            'storage://',
            'theme://',
            'app://config.php',
            'app://app',
        );

        $result = array();

        foreach ($directories as $directory) {
            $path = $this('locator')->findResource($directory);

            $result[$this->getRelativePath($path)] = is_writable($path);

            if (is_dir($path)) {
                foreach (Finder::create()->in($path)->directories()->depth(0) as $folder) {
                    $result[$this->getRelativePath($folder->getPathname())] = is_writable($folder->getPathname());
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
        if (0 === strpos($path, $this('path'))) {
            $path = ltrim(str_replace('\\', '/', substr($path, strlen($this('path')))), '/');
        }

        return $path;
    }
}