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
namespace Cascade\Tests\Config\Loader\ClassLoader;

use Cascade\Config\Loader\ClassLoader\FormatterLoader;

/**
 * Class FormatterLoaderTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class FormatterLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up function
     */
    public function setUp()
    {
        parent::setUp();
        new FormatterLoader(array());
    }

    /**
     * Tear down function
     */
    public function tearDown()
    {
        FormatterLoader::$extraOptionHandlers = array();
        parent::tearDown();
    }

    /**
     * Check if the handler exists for a given class and option
     * Also checks that it a callable and return it
     *
     * @param  string $class Class name the handler applies to
     * @param  string $optionName Option name
     * @return \Closure Closure
     * @throws \Exception
     */
    private function getHandler($class, $optionName)
    {
        if (isset(FormatterLoader::$extraOptionHandlers[$class][$optionName])) {
            // Get the closure
            $closure = FormatterLoader::$extraOptionHandlers[$class][$optionName];

            $this->assertTrue(is_callable($closure));

            return $closure;
        } else {
            throw new \Exception(
                sprintf(
                    'Handler %s is not defined for class %s',
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
     * @param  string $class Class name
     * @param  string $methodName Method name
     * @param  mixed $methodArg Parameter passed to the closure
     * @param  \Closure $closure Closure to call
     */
    private function doTestMethodCalledInHandler($class, $methodName, $methodArg, \Closure $closure)
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
        $this->assertTrue(count(FormatterLoader::$extraOptionHandlers) > 0);
    }

    /**
     * Data provider for testHandlers
     * /!\ Important note: just add values to this array if you need to test a newly added handler
     * If one of your handlers calls more than one method you can add more than one entries
     *
     * @return array of array of args for testHandlers
     */
    public function handlerParamsProvider()
    {
        return array(
            array(
                'Monolog\Formatter\LineFormatter',   // Class name
                'includeStacktraces',                // Option name
                true,                                // Option test value
                'includeStacktraces'                 // Name of the method called by your handler
            )
        );
    }

    /**
     * Test the extra option handlers
     * @see doTestMethodCalledInHandler
     *
     * @param  string $class Class name
     * @param  string $optionName Option name
     * @param  mixed $optionValue Option value
     * @param  string $calledMethodName Expected called method name
     * @dataProvider handlerParamsProvider
     */
    public function testHandlers($class, $optionName, $optionValue, $calledMethodName)
    {
        // Test if handler exists and return it
        $closure = $this->getHandler($class, $optionName);

        $this->doTestMethodCalledInHandler($class, $calledMethodName, $optionValue, $closure);
    }
}
