<?php

namespace Pagekit\Filesystem\Adapter;

class FileAdapter implements AdapterInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $url;

    /**
     * Constructor.
     *
     * @param string $path;
     * @param string $url;
     */
    public function __construct($path, $url = '')
    {
        $this->path = strtr($path, '\\', '/');
        $this->url  = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getStreamWrapper()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPathInfo(array $info)
    {
        $info['localpath'] = $info['pathname'];

        if ($info['root'] === '') {

            $path = $this->path;

            if (substr($path, -1) != '/') {
                $path .= '/';
            }

            $info['localpath'] = $path.$info['path'];
        }

        if ($info['localpath'] and file_exists($info['localpath'])) {
            if (strpos($info['localpath'], $this->path) === 0) {
                $info['url'] = $this->url.strtr(rawurlencode(substr($info['localpath'], strlen($this->path))), ['%2F' => '/']);
            }
        }

        return $info;
    }
}
