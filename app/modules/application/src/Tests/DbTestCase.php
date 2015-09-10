<?php

namespace Pagekit\Tests;

abstract class DbTestCase extends \PHPUnit_Framework_TestCase
{
    use DbUtil;

    protected $connection;

    public function setUp()
    {
        try {

            $this->connection = $this->getSharedConnection();

        } catch (\Exception $e) {
            $this->markTestSkipped(sprintf('Unable to establish connection. (%s)', $e->getMessage()));
            return;
        }
    }
}