<?php
/**
 * This file is part of the Monolog Cascade package.
 *
 * (c) Raphael Antonmattei <rantonmattei@theorchard.com>
 * (c) The Orchard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cascade\Tests\Config;

use Cascade\Config\ConfigLoader;
use Cascade\Tests\Fixtures;

/**
 * Class ConfigLoaderTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class ConfigLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Loader to test against
     * @var ConfigLoader
     */
    protected $loader = null;

    public function setUp()
    {
        parent::setup();
        $this->loader = new ConfigLoader();
    }

    public function tearDown()
    {
        $this->loader = null;
        parent::tearDown();
    }

    public function testLoader()
    {
        $this->assertInstanceOf(
            'Symfony\Component\Config\Loader\DelegatingLoader',
            $this->loader
        );

        $this->assertInstanceOf(
            'Symfony\Component\Config\Loader\LoaderResolver',
            $this->loader->getResolver()
        );

        $configLoaders = $this->loader->getResolver()->getLoaders();
        $this->assertCount(4, $configLoaders);

        // Checking the order of thr loaders
        $this->assertInstanceOf(
            'Cascade\Config\Loader\PhpArray',
            $configLoaders[0]
        );
        $this->assertInstanceOf(
            'Cascade\Config\Loader\FileLoader\PhpArray',
            $configLoaders[1]
        );
        $this->assertInstanceOf(
            'Cascade\Config\Loader\FileLoader\Json',
            $configLoaders[2]
        );
        $this->assertInstanceOf(
            'Cascade\Config\Loader\FileLoader\Yaml',
            $configLoaders[3]
        );
    }

    public function testLoad()
    {
        $json = Fixtures::getSampleJsonString();
        $this->assertEquals(json_decode($json, true), $this->loader->load($json));
    }
}
