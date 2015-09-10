<?php

namespace Pagekit\Filter\Tests;

use Pagekit\Filter\AlphaFilter;

class AlphaTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $filter = new AlphaFilter;

        $values = [
            /* here are the ones the filter should not change */
            "abc"   => "abc",
            "äöü"   => "äöü", // unicode support please
            /* now the ones the filter has to fix */
            ""   => "",
            "?"     => "",
            "abc!"  => "abc",
            "     " => "",
            "!§$%&/()="   => "",
            "abc123!?) abc" => "abcabc"
        ];
        foreach ($values as $in => $out) {
            $this->assertEquals($filter->filter($in), $out);
        }

    }

}
