<?php

namespace Pagekit\Filter\Tests;

use Pagekit\Filter\FilterChain;

class FilterChainTest extends \PHPUnit_Framework_TestCase
{
    public function testAttach()
    {
        $chain = new FilterChain;

        $chain->attach(function($value) { return $value; });
        $this->assertCount(1, $chain->getFilters());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAttachFailed()
    {
        $chain = new FilterChain;

        $chain->attach(new \stdClass);
    }

    public function testMerge()
    {
        $chain = new FilterChain;
        $chain->attach($this->getFilterMock());

        $chain2 = new FilterChain;
        $chain2->attach($this->getFilterMock());
        $chain->merge($chain2);

        $this->assertCount(2, $chain->getFilters());
    }

    public function testCount()
    {
        $chain = new FilterChain;
        $this->assertCount(0, $chain);

        $chain->attach($this->getFilterMock());
        $this->assertCount(1, $chain);

        $chain->attach($this->getFilterMock());
        $this->assertCount(2, $chain);
    }

    public function testFilter()
    {
        $chain = new FilterChain;
        $chain->attach(function($value) { return 'filtered_'.$value; });

        $value = 'TEST';
        $this->assertEquals('filtered_TEST', $chain->filter($value));
    }

    protected function getFilterMock()
    {
        $filter = $this->getMock('Pagekit\Filter\FilterInterface');
        $filter->expects($this->any())
               ->method('filter');

        return $filter;
    }
}
