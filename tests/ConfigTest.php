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

use Monolog\Registry;

use Cascade\Config;
use Cascade\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class ConfigTest extends TestCase
{
    /**
     * Testing contructor and load functions
     */
    public function testLoad()
    {
        $mock = $this->getMockBuilder('Cascade\Config\ConfigLoader')
            ->disableOriginalConstructor()
            ->onlyMethods(array('load'))
            ->getMock();

        $array = Fixtures::getSamplePhpArray();

        $mock->expects($this->once())
            ->method('load')
            ->willReturn($array);

        $config = new Config(array(''), $mock);
        $config->load();
    }

    public function testConfigure()
    {
        $options = Fixtures::getPhpArrayConfig();

        // Mocking the ConfigLoader with the load method
        $configLoader = $this->getMockBuilder('Cascade\Config\ConfigLoader')
            ->disableOriginalConstructor()
            ->onlyMethods(array('load'))
            ->getMock();

        $configLoader->method('load')->willReturn($options);

        // Mocking the config object and set expectations for the configure methods
        $config = $this->getMockBuilder('Cascade\Config')
            ->setConstructorArgs(array($options, $configLoader))
            ->onlyMethods(array(
                    'configureFormatters',
                    'configureProcessors',
                    'configureHandlers',
                    'configureLoggers'
                ))
            ->getMock();

        $config->expects($this->once())->method('configureFormatters');
        $config->expects($this->once())->method('configureProcessors');
        $config->expects($this->once())->method('configureHandlers');
        $config->expects($this->once())->method('configureLoggers');

        $config->load();
        $config->configure();
    }

    /**
     * Test configure throwing an exception due to missing 'loggers' key
     */
    public function testConfigureWithNoLoggers()
    {
        $this->expectException(\RuntimeException::class);

        $options = array();

        // Mocking the ConfigLoader with the load method
        $configLoader = $this->getMockBuilder('Cascade\Config\ConfigLoader')
            ->disableOriginalConstructor()
            ->onlyMethods(array('load'))
            ->getMock();

        $configLoader->method('load')->willReturn($options);

        // Mocking the config object
        $config = $this->getMockBuilder('Cascade\Config')
            ->setConstructorArgs(array($options, $configLoader))
            ->onlyMethods([])
            ->getMock();

        $config->load();

        // This should trigger an exception because there is no 'loggers' key in
        // the options passed in
        $config->configure();
    }

    public function testLoggersConfigured()
    {
        $options = Fixtures::getPhpArrayConfig();

        // Mocking the ConfigLoader with the load method
        $configLoader = $this->getMockBuilder('Cascade\Config\ConfigLoader')
            ->disableOriginalConstructor()
            ->onlyMethods(array('load'))
            ->getMock();

        $configLoader->method('load')->willReturn($options);

        $config = new Config($options, $configLoader);

        $config->load();
        $config->configure();

        $this->assertTrue(Registry::hasLogger('my_logger'));
    }
}
