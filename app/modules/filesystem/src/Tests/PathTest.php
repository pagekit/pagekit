<?php

namespace Pagekit\Filesystem\Tests;

use Pagekit\Filesystem\Path;

class PathTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataPaths
     */
    public function testParse($path, $result)
    {
        $this->assertSame($result, Path::parse($path));
    }

    /**
     * @dataProvider dataPaths
     */
    public function testIsAbsolute($path, $result)
    {
        if ($result['root'] !== '') {
            $this->assertTrue(Path::isAbsolute($path));
        } else {
            $this->assertFalse(Path::isAbsolute($path));
        }
    }

    /**
     * @dataProvider dataPaths
     */
    public function testIsRelative($path, $result)
    {
        if ($result['root'] === '') {
            $this->assertTrue(Path::isRelative($path));
        } else {
            $this->assertFalse(Path::isRelative($path));
        }
    }

    public function dataPaths()
    {
        return [
            ['dir/file.txt', ['root' => '', 'path' => 'dir/file.txt', 'dirname' => 'dir', 'pathname' => 'dir/file.txt', 'protocol' => 'file']],
            ['dir/./file.txt', ['root' =>'', 'path' => 'dir/file.txt', 'dirname' => 'dir', 'pathname' => 'dir/file.txt', 'protocol' => 'file']],
            ['dir/../file.txt', ['root' => '', 'path' => 'file.txt', 'dirname' => '', 'pathname' => 'file.txt', 'protocol' => 'file']],
            ['/dir/file.txt', ['root' => '/', 'path' => 'dir/file.txt', 'dirname' => '/dir', 'pathname' => '/dir/file.txt', 'protocol' => 'file']],
            ['C:\dir\file.txt', ['root' => 'C:/', 'path' => 'dir/file.txt', 'dirname' => 'C:/dir', 'pathname' => 'C:/dir/file.txt', 'protocol' => 'file']],
            ['http://dir/file.txt', ['root' => 'http://', 'path' => 'dir/file.txt', 'dirname' => 'http://dir', 'pathname' => 'http://dir/file.txt', 'protocol' => 'http']]
        ];
    }
}
