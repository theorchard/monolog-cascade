<?php
namespace Cascade\Tests;

use Monolog\Logger;
use Monolog\Registry;

use Cascade\MonoLogger;
use Cascade\Tests\Fixtures;

/**
 * Class MonoLoggerTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class MonoLoggerTest extends \PHPUnit_Framework_TestCase
{
    public function teardown()
    {
        Registry::clear();
        parent::teardown();
    }

    public function testRegistry()
    {
        $logger = MonoLogger::getLogger('test');

        $this->assertTrue($logger instanceof Logger);
        $this->assertEquals('test', $logger->getName());
        $this->assertTrue(Registry::hasLogger('test'));

        // We should get the logger from the registry this time
        $logger2 = MonoLogger::getLogger('test');
        $this->assertSame($logger, $logger2);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRegistryWithInvalidName()
    {
        $logger = MonoLogger::getLogger(null);
    }

    public function testFileConfig()
    {
        $options = Fixtures::getPhpArrayConfig();
        MonoLogger::fileConfig($options);
        $this->assertInstanceOf('Cascade\Config', MonoLogger::getConfig());
    }
}
