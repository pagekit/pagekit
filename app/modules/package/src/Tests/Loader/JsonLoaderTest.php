<?php

namespace Pagekit\Package\Tests\Loader;

use Pagekit\Package\Loader\JsonLoader;

class JsonLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $loader;

    public function setUp()
    {
        $this->loader = new JsonLoader;
    }

    /**
     * @dataProvider getKeys
     */
    public function testLoadFromFile($key, $value)
    {
        $package = $this->loader->load(__DIR__.'/../Fixtures/Package/extension.json');

        $this->assertEquals($value, call_user_func([$package, 'get'.ucfirst($key)]));
    }

    /**
     * @dataProvider getKeys
     */
    public function testLoadFromString($key, $value)
    {
        $package = $this->loader->load(file_get_contents(__DIR__.'/../Fixtures/Package/extension.json'));

        $this->assertEquals($value, call_user_func([$package, 'get'.ucfirst($key)]));
    }

    public function getKeys()
    {
        return [
            [
                'name',
                'test'
            ],
            [
                'version',
                '0.0.1'
            ],
            [
                'type',
                'extension'
            ],
            [
                'title',
                'Test'
            ],
            [
                'authors',
                null
            ],
            [
                'homepage',
                'http://pagekit.com'
            ],
            [
                'description',
                'Test Extension Package ...'
            ],
            [
                'license',
                ['MIT']
            ]
        ];
    }    
}
