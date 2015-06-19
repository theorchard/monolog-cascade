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
namespace Cascade\Tests;

use Monolog\Logger;
use Monolog\Registry;

use Cascade\Cascade;
use Cascade\Tests\Fixtures;

/**
 * Class CascadeTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class CascadeTest extends \PHPUnit_Framework_TestCase
{
    public function teardown()
    {
        Registry::clear();
        parent::teardown();
    }

    public function testCreateLogger()
    {
        $logger = Cascade::createLogger('test');

        $this->assertTrue($logger instanceof Logger);
        $this->assertEquals('test', $logger->getName());
        $this->assertTrue(Registry::hasLogger('test'));
    }

    public function testRegistry()
    {
        // Creates the logger and push it to the registry
        $logger = Cascade::logger('test');

        // We should get the logger from the registry this time
        $logger2 = Cascade::logger('test');
        $this->assertSame($logger, $logger2);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRegistryWithInvalidName()
    {
        $logger = Cascade::getLogger(null);
    }

    public function testFileConfig()
    {
        $options = Fixtures::getPhpArrayConfig();
        Cascade::fileConfig($options);
        $this->assertInstanceOf('Cascade\Config', Cascade::getConfig());
    }
}
