<?php

namespace Pagekit\Database\Tests\Logging;

use Pagekit\Database\Logging\DebugStack;

class DebugStackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DebugStack
     */
    protected $logger;

    public function setUp()
	{
		$this->logger = new DebugStack;
	}

	public function testLogging()
	{
		$this->logger->startQuery('SELECT something FROM table');
		$this->assertEquals([
            1 => [
                'sql'         => 'SELECT something FROM table',
                'params'      => null,
                'types'       => null,
                'executionMS' => 0]
            ],
            $this->logger->queries
        );

		$this->logger->stopQuery();
		$this->assertGreaterThan(0, $this->logger->queries[1]['executionMS']);
		$this->assertGreaterThan(0, str_word_count($this->logger->queries[1]['callstack']));
	}

	public function testDisabledLogger()
	{
		$this->logger->enabled = false;
		$this->logger->startQuery('SELECT something FROM table');
		$this->assertEquals([], $this->logger->queries);

		$this->logger->stopQuery();
		$this->assertEquals([], $this->logger->queries);
	}
}
