<?php

namespace Pagekit\Installer\Installer;

class Verifier
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
     * Verifies integrity of a given string.
     *
     * @param string $data
     * @param string $token
     * @return bool
     */
    public function verify($data, $token)
    {
        return sha1($this->hash($data)) === sha1($token);
    }

    /**
     * Calculates HMAC-SHA1 message authentication code for given data.
     *
     * @param string $data
     * @return string
     */
    public function hash($data)
    {
        return base64_encode(extension_loaded('hash') ?
            hash_hmac('sha1', $data, $this->secretKey, true) : pack('H*', sha1(
                (str_pad($this->secretKey, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) .
                pack('H*', sha1((str_pad($this->secretKey, 64, chr(0x00)) ^
                        (str_repeat(chr(0x36), 64))) . $data)))));
    }
}