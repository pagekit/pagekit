<?php

namespace Pagekit\Filter\Tests;

class StripNewlinesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideNewLineStrings
     */
    public function testFilter($input, $output)
    {
        $filter = new \Pagekit\Filter\StripNewlines;

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
