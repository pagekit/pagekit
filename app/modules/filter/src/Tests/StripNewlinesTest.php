<?php

namespace Pagekit\Filter\Tests;

use Pagekit\Filter\StripNewlinesFilter;

class StripNewlinesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideNewLineStrings
     */
    public function testFilter($input, $output)
    {
        $filter = new StripNewlinesFilter;

        $this->assertEquals($output, $filter->filter($input));
    }

    /**
     * @return array
     */
    public function provideNewLineStrings()
    {
        return [
            ['', ''],
            ["\n", ''],
            ["\r", ''],
            ["\r\n", ''],
            ['\n', '\n'],
            ['\r', '\r'],
            ['\r\n', '\r\n'],
            ["These newlines should\nbe removed by\r\nthe filter", 'These newlines shouldbe removed bythe filter']
        ];
    }
}
