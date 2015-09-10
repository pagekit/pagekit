<?php

namespace Pagekit\Filter\Tests;

use Pagekit\Filter\SlugifyFilter;

class SlugifyTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $filter = new SlugifyFilter;

        $values = [
            'PAGEKIT'                  => 'pagekit',
            ":#*\"@+=;!><&.%()/'\\|[]" => "",
            "  a b ! c   "             => "a-b-c",
        ];

        foreach ($values as $in => $out) {
            $this->assertEquals($out, $filter->filter($in));
        }

    }
}
