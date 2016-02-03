<?php

namespace Pagekit\Filesystem;

use Pagekit\Filesystem\Adapter\AdapterInterface;
use Pagekit\Routing\Generator\UrlGenerator;

class Filesystem
{
    /**
     * @var AdapterInterface[]
     */
    protected $adapters = [];

    /**
     * Gets file path URL.
     *
     * @param  string $file
     * @param  mixed  $referenceType
     * @return string|false
     */
    public function getUrl($file, $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        if (!$url = $this->getPathInfo($file, 'url')) {
            return false;
        }

        if ($referenceType === UrlGenerator::ABSOLUTE_PATH) {
            $url = strlen($path = parse_url($url, PHP_URL_PATH)) > 1 ? substr($url, strpos($url, $path)) : '/';
        } elseif ($referenceType === UrlGenerator::NETWORK_PATH) {
            $url = substr($url, strpos($url, '//'));
        }

        return $url;
    }

    /**
     * Gets canonicalized file path or localpath.
     *
     * @param  string $file
     * @param  bool   $local
     * @return string|false
     */
    public function getPath($file, $local = false)
    {
        return $this->getPathInfo($file, $local ? 'localpath' : 'pathname') ?: false;
    }

    /**
     * Gets file path info.
     *
     * @param  string $file
     * @param  string $option
     * @return string|array
     */
    public function getPathInfo($file, $option = null)
    {
        $info = Path::parse($file);

        if ($info['protocol'] != 'file') {
            $info['url'] = $info['pathname'];
        }

        if ($adapter = $this->getAdapter($info['protocol'])) {
            $info = $adapter->getPathInfo($info);
        }

        if ($option === null) {
            return $info;
        }

        return array_key_exists($option, $info) ? $info[$option] : '';
    }

    /**
     * Checks whether a file or directory exists.
     *
     * @param  string|array $files
     * @return bool
     */
    public function exists($files)
    {
        $files = (array) $files;

        foreach ($files as $file) {

            $file = $this->getPathInfo($file, 'pathname');

            if (!file_exists($file)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Copies a file.
     *
     * @param  string $source
     * @param  string $target
     * @return bool
     */
    public function copy($source, $target)
    {
        $source = $this->getPathInfo($source, 'pathname');
        $target = $this->getPathInfo($target);

        if (!is_file($source) || !$this->makeDir($target['dirname'])) {
            return false;
        }

        return @copy($source, $target['pathname']);
    }

    /**
     * Deletes a file.
     *
     * @param  string|array $files
     * @return bool
     */
    public function delete($files)
    {
        $files = (array) $files;

        foreach ($files as $file) {

            $file = $this->getPathInfo($file, 'pathname');

            if (is_dir($file)) {

                if (substr($file, -1) != '/') {
                    $file .= '/';
                }

                foreach ($this->listDir($file) as $name) {
                    if (!$this->delete($file.$name)) {
                        return false;
                    }
                }

                if (!@rmdir($file)) {
                    return false;
                }

            } elseif (!@unlink($file)) {
                return false;
            }
        }

        return true;
    }

    /**
     * List files and directories inside the specified path.
     *
     * @param  string $dir
     * @return array
     */
    public function listDir($dir)
    {
        $dir = $this->getPathInfo($dir, 'pathname');

        return array_diff(scandir($dir) ?: [], ['..', '.']);
    }

    /**
     * Makes a directory.
     *
     * @param  string $dir
     * @param  int    $mode
     * @param  bool   $recursive
     * @return bool
     */
    public function makeDir($dir, $mode = 0777, $recursive = true)
    {
        $dir = $this->getPathInfo($dir, 'pathname');

        return is_dir($dir) ? true : @mkdir($dir, $mode, $recursive);
    }

    /**
     * Copies a directory.
     *
     * @param  string $source
     * @param  string $target
     * @return bool
     */
    public function copyDir($source, $target)
    {
        $source = $this->getPathInfo($source, 'pathname');
        $target = $this->getPathInfo($target, 'pathname');

        if (!is_dir($source) || !$this->makeDir($target)) {
            return false;
        }

        if (substr($source, -1) != '/') {
            $source .= '/';
        }

        if (substr($target, -1) != '/') {
            $target .= '/';
        }

        foreach ($this->listDir($source) as $file) {
            if (is_dir($source.$file)) {

                if (!$this->copyDir($source.$file, $target.$file)) {
                    return false;
                }

            } elseif (!$this->copy($source.$file, $target.$file)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Gets a adapter.
     *
     * @param  string $protocol
     * @return AdapterInterface|null
     */
    public function getAdapter($protocol)
    {
        return isset($this->adapters[$protocol]) ? $this->adapters[$protocol] : null;
    }

    /**
     * Registers a adapter.
     *
     * @param string           $protocol
     * @param AdapterInterface $adapter
     */
    public function registerAdapter($protocol, AdapterInterface $adapter)
    {
        $this->adapters[$protocol] = $adapter;

        if ($wrapper = $adapter->getStreamWrapper()) {
            stream_wrapper_register($protocol, $wrapper);
        }
    }
}
