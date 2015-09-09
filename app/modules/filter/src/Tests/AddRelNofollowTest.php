<?php

namespace Pagekit\Filter\Tests;

use Pagekit\Filter\AddRelNofollowFilter;

class AddRelNofollowTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $filter = new AddRelNofollowFilter;

        $this->assertTrue(false !== strpos($filter->filter('<a href="http://www.example.com/">text</a>'), 'rel="nofollow"'));
        $this->assertTrue(false !== strpos($filter->filter('<A href="http://www.example.com/">text</a>'), 'rel="nofollow"'));

        // TODO: these tests should validate too
//        $this->assertTrue(false !== strpos($filter->filter('<a/href=\"http://www.example.com/\">text</a>'), 'rel="nofollow"'));
//        $this->assertTrue(false !== strpos($filter->filter('<\0a\0 href=\"http://www.example.com/\">text</a>'), 'rel="nofollow"'));
//        $this->assertFalse(strpos($filter->filter('<a href="http://www.example.com/" rel="follow">text</a>'), 'rel="follow"'));
    }

}
