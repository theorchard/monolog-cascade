<?php
/**
 * This file is part of the Monolog Cascade package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cascade\Tests\Config\Loader\FileLoader;

use Symfony\Component\Config\FileLocator;

use Cascade\Config\Loader\FileLoader\PhpArray as ArrayLoader;

/**
 * Class PhpArrayTest
 */
class PhpArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayLoader
     */
    protected $loader;

    protected function setUp()
    {
        $this->loader = new ArrayLoader(new FileLocator());
    }

    protected function tearDown()
    {
        $this->loader = null;
    }

    public function testSupportsPhpFile()
    {
        $this->assertTrue($this->loader->supports(__DIR__.'/../../../Fixtures/fixture_config.php'));
    }

    public function testDoesNotSupportNonPhpFiles()
    {
        $this->assertFalse($this->loader->supports('foo'));
        $this->assertFalse($this->loader->supports(__DIR__.'/../../../Fixtures/fixture_config.json'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionWhenLoadingFileIfDoesNotReturnValidPhpArray()
    {
        $this->loader->load(__DIR__.'/../../../Fixtures/fixture_invalid_config.php');
    }

    public function testLoadsPhpArrayConfigFromFile()
    {
        $this->assertSame(
            include __DIR__.'/../../../Fixtures/fixture_config.php',
            $this->loader->load(__DIR__.'/../../../Fixtures/fixture_config.php')
        );
    }
}
