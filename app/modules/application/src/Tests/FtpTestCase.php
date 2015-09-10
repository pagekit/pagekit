<?php

namespace Pagekit\Tests;

abstract class FtpTestCase extends \PHPUnit_Framework_TestCase
{
    use FtpUtil;

    protected $workspace = null;
    protected $mode;
    protected $connection;

    public function setUp()
    {
        try {

            $this->connection = $this->getSharedFtpConnection();

        } catch (\Exception $e) {
            $this->markTestSkipped(sprintf('Unable to establish connection. (%s)', $e->getMessage()));
            return;
        }

        $this->mode = $GLOBALS['ftp_mode'] == 'FTP_ASCII' ? FTP_ASCII : FTP_BINARY;

        $this->workspace = DIRECTORY_SEPARATOR.time().rand(0, 1000);

        if (false === @ftp_mkdir($this->connection, $this->workspace)) {
            $this->markTestSkipped('Unable to create workspace folder');
            $this->workspace = false;
            return;
        }
    }

    public function tearDown()
    {
        if (is_resource($this->connection)) {
            if ($this->workspace) {
                $this->clean($this->workspace);
            }
        }
    }

    /**
     * @param string $file
     */
    private function clean($file)
    {
        if (ftp_size($this->connection, $file) == -1) {
            $result = ftp_nlist($this->connection, $file);
            foreach ($result as $childFile) {
                $this->clean($childFile);
            }
            ftp_rmdir($this->connection, $file);
        } else {
            ftp_delete($this->connection, $file);
        }
    }
}