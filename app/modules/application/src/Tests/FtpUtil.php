<?php

namespace Pagekit\Tests;

trait FtpUtil
{
    /**
     * @return null|ftp connection resource
     */
    public function getFtpConnection()
    {
        if (!extension_loaded('ftp')) {
            throw new \Exception('FTP extension needed');
        }

        if (!function_exists('ftp_connect')) {
            throw new \Exception('Function "ftp_connect" does not exist on this server.');
        }

        $connection = null;
        if (!isset($GLOBALS['ftp_host'], $GLOBALS['ftp_port'], $GLOBALS['ftp_user'], $GLOBALS['ftp_pass'], $GLOBALS['ftp_passive'], $GLOBALS['ftp_mode'])) {
            throw new \Exception('FTP credentials not set.');
        }

        if (false === $connection = ftp_connect($GLOBALS['ftp_host']) or !is_resource($connection)) {
            throw new \Exception('Unable to connect to ftp server.');
        }

        if (false === ftp_login($connection, $GLOBALS['ftp_user'], $GLOBALS['ftp_pass'])) {
            ftp_close($connection);
            throw new \Exception('Unable to login to ftp server.');
        }

        // switch to passive mode if needed
        if ($GLOBALS['ftp_passive'] && !ftp_pasv($connection, true)) {
            ftp_close($connection);
            throw new \Exception('Unable to switch on FTP passive mode.');
        }

        return $connection;
    }

    public function getSharedFtpConnection()
    {
        static $connection;
        static $error;

        if (!isset($connection) && !isset($error)) {

            try {
                $connection = $this->getFtpConnection();
            } catch (\Exception $e) {
                $error = $e;
            }

            if (!is_resource($connection)) {
                $error = new \Exception('Unable to establish connection.');
            }
        }

        if (isset($error)) {
            throw $error;
        }

        return $connection;
    }
}