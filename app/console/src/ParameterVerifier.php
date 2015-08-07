<?php

namespace Pagekit\Console;

class ParameterVerifier
{

    protected $secretKey;

    /**
     * @param $config
     */
    public function __construct($config)
    {
        $this->secretKey = sha1(json_encode(array_merge([__DIR__], $config)));
    }

    /**
     * Verifies integrity of parameter array.
     *
     * @param $params
     * @return bool
     */
    public function verify(array $params)
    {
        if (!isset($params['expires'], $params['token']) || $params['expires'] < time()) {
            return false;
        }

        return sha1($this->hash(array_diff_key($params, ['token' => false]))) === sha1($params['token']);
    }

    /**
     * Calculates HMAC-SHA1 message authentication code for parameter array.
     *
     * @param array $params
     * @return string
     */
    public function hash(array $params)
    {
        ksort($params);
        $string = json_encode($params, JSON_NUMERIC_CHECK);

        return base64_encode(extension_loaded('hash') ?
            hash_hmac('sha1', $string, $this->secretKey, true) : pack('H*', sha1(
                (str_pad($this->secretKey, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) .
                pack('H*', sha1((str_pad($this->secretKey, 64, chr(0x00)) ^
                        (str_repeat(chr(0x36), 64))) . $string)))));
    }

}