<?php
/**
 * This file is part of the Monolog Cascade package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cascade\Tests\Config\Loader\FileLoader;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;

use Cascade\Config\Loader\FileLoader\PhpArray as ArrayLoader;

/**
 * Class PhpArrayTest
 */
class PhpArrayTest extends TestCase
{
    /**
     * @var ArrayLoader
     */
    protected $loader;

    protected function setUp(): void
    {
        $this->loader = new ArrayLoader(new FileLocator());
    }

    protected function tearDown(): void
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

    public function testThrowsExceptionWhenLoadingFileIfDoesNotReturnValidPhpArray()
    {
        $this->expectException(\InvalidArgumentException::class);

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
