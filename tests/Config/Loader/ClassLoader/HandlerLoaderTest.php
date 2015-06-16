<?php
namespace Cascade\Tests\Config\Loader\ClassLoader;

use Monolog\Formatter\LineFormatter;

use Cascade\Config\Loader\ClassLoader\HandlerLoader;

/**
 * Class HandlerLoaderTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class HandlerLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testHandlerLoader()
    {
        $original = $options = array(
            'class' => '\Monolog\Handler\TestHandler',
            'level' => 'DEBUG',
            'formatter' => 'test_formatter'
        );
        $formatters = array('test_formatter' => new LineFormatter());
        $loader = new HandlerLoader($options, $formatters);

        $this->assertNotEquals($original, $options);
        $this->assertEquals(new LineFormatter(), $options['formatter']);
    }

    public function testHandlerLoaderWithNoOptions()
    {
        $original = $options = array();
        $loader = new HandlerLoader($options);

        $this->assertEquals($original, $options);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testHandlerLoaderWithInvalidFormatter()
    {
        $options = array(
            'formatter' => 'test_formatter'
        );

        $formatters = array('test_formatterXYZ' => new LineFormatter());
        $loader = new HandlerLoader($options, $formatters);
    }

    /**
     * Check if the handler exists for a given class and option
     * Also checks that it a callable and return it
     *
     * @param  string $class Class name the handler applies to
     * @param  string $optionName Option name
     * @return \Closure Closure
     */
    private function getHandler($class, $optionName)
    {
        if (isset(HandlerLoader::$extraOptionHandlers[$class][$optionName])) {
            // Get the closure
            $closure = HandlerLoader::$extraOptionHandlers[$class][$optionName];

            $this->assertTrue(is_callable($closure));

            return $closure;
        } else {
            throw new \Exception(
                sprintf(
                    'Custom handler %s is not defined for class %s',
                    $optionName,
                    $class
                )
            );
        }
    }

    /**
     * Tests that calling the given Closure will trigger a method call with the given param
     * in the given class
     *
     * @param  string   $class      Class name
     * @param  string   $methodName Method name
     * @param  mixed    $methodArg  Parameter passed to the closure
     * @param  \Closure $closure    Closure to call
     */
    private function _testMethodCalledInHandler($class, $methodName, $methodArg, \Closure $closure)
    {
        // Setup mock and expectations
        $mock = $this->getMockBuilder($class)
            ->setMethods(array($methodName))
            ->getMock();

        $mock->expects($this->once())
            ->method($methodName)
            ->with($methodArg);

        // Calling the handler
        $closure($mock, $methodArg);
    }


    /**
     * Test that handlers exist
     */
    public function testHandlersExist()
    {
        $options = array();
        new HandlerLoader($options);
        $this->assertTrue(count(HandlerLoader::$extraOptionHandlers) > 0);
    }

    /**
     * Data provider for testHandlers
     * /!\ Important note:
     * Just add values to this array if you need to test a newly added handler
     *
     * If one of your handlers calls more than one method you can add more than one entries
     *
     * @return array of array of args for testHandlers
     */
    public function handlerParamsProvider()
    {
        return array(
            array(
                '*',                    // Class name
                'formatter',            // Option name
                new LineFormatter(),    // Option test value
                'setFormatter'          // Name of the method called by your handler
            )
        );
    }

    /**
     * Test the extra option handlers
     *
     * @dataProvider handlerParamsProvider
     */
    public function testHandlers($class, $optionName, $optionValue, $calledMethodName)
    {
        $options = array();
        new HandlerLoader($options);
        // Test if handler exists and return it
        $closure = $this->getHandler($class, $optionName);

        if ($class == '*') {
            $class = '\Monolog\Handler\TestHandler';
        }

        $this->_testMethodCalledInHandler($class, $calledMethodName, $optionValue, $closure);
    }
}
