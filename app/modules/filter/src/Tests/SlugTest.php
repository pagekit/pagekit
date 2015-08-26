<?php

namespace Pagekit\Filter\Tests;

use Pagekit\Filter\Slug;

class SlugTest extends \PHPUnit_Framework_TestCase
{

    public function testFilter()
    {
        $filter = new Slug;

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
