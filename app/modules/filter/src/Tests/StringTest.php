<?php

namespace Pagekit\Filter\Tests;

use Pagekit\Filter\StringFilter;

class StringTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $filter = new StringFilter;

        $values = [
            23                  => "23",
            "23"                => "23",
            '"23"'              => '"23"',
            '{"foo": "23"}'     => '{"foo": "23"}',
            "whateverthisis"    => "whateverthisis",
            "!'#+*§$%&/()=?"    => "!'#+*§$%&/()=?",
            'äöü'               => "äöü" // unicode support please
        ];
        foreach ($values as $in => $out) {
            $this->assertSame($filter->filter($in), $out);
        }

    }

}
