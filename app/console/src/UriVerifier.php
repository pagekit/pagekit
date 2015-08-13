<?php

namespace Pagekit\Console;

class UriVerifier
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
     * Verifies integrity of URI.
     *
     * @param string $uri
     * @return bool
     */
    public function verify($uri)
    {
        $uri = preg_replace_callback('/&token=([^&]+)/i',
            function ($matches) use (&$token) {
                $token = $matches[1];

                return '';
            }, $uri);

        if (!$uri || !$token || !preg_match('/(?<=expires=)[^&]+/i', $uri, $expires) || $expires[0] < time()) {
            return false;
        };

        return sha1($this->hash($uri)) === sha1($token);
    }

    /**
     * Signs a given URI.
     *
     * @param string $uri
     * @param $lifetime
     * @return string
     */
    public function sign($uri, $lifetime)
    {
        $uri = sprintf('%s%sexpires=%u', $uri, stripos($uri, '?') ? '&' : '?', time() + $lifetime);

        return sprintf('%s&token=%s', $uri, $this->hash($uri));
    }

    /**
     * Calculates HMAC-SHA1 message authentication code for URI.
     *
     * @param string $uri
     * @return string
     */
    public function hash($uri)
    {
        $uri = preg_replace('/https?:\/{2}/i', '', $uri);

        return base64_encode(extension_loaded('hash') ?
            hash_hmac('sha1', $uri, $this->secretKey, true) : pack('H*', sha1(
                (str_pad($this->secretKey, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) .
                pack('H*', sha1((str_pad($this->secretKey, 64, chr(0x00)) ^
                        (str_repeat(chr(0x36), 64))) . $uri)))));
    }

}