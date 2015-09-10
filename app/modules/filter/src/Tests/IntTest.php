<?php

namespace Pagekit\Filter\Tests;

use Pagekit\Filter\IntFilter;

class IntTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $filter = new IntFilter;

        $values = [
            "23"    => 23,
            "-23"   => -23,
            "123"   => 123,
            "012"   => 12,
            "äöü"   => 0, // unicode support please
            "2147483647" => 2147483647, // largest INT that php can handle
            "abc123!?) abc" => 0
        ];
        foreach ($values as $in => $out) {
            $this->assertSame($filter->filter($in), $out);
            $this->assertTrue(is_int($out));
        }

    }

}
