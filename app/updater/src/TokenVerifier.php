<?php

namespace Pagekit\Updater;

class TokenVerifier
{

    protected $key;

    /**
     * @param $config
     */
    public function __construct($config)
    {
        $this->key = json_encode($config);
    }

    /**
     * @param $hash
     * @param $params
     * @return bool
     */
    public function verify($hash, $params)
    {
        return $this->hash($params) === $hash;
    }

    /**
     * @param $params
     * @return string
     */
    public function hash($params)
    {
        return hash('sha256', json_encode($params) + $this->key);
    }

}