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
namespace Cascade\Tests\Config\Loader;

use Cascade\Config\Loader\ClassLoader;
use Cascade\Tests\Fixtures\DependentClass;
use Cascade\Tests\Fixtures\SampleClass;

/**
 * Class ClassLoaderTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 * @author Dom Morgan <dom@d3r.com>
 */
class ClassLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up function
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Tear down function
     */
    public function tearDown()
    {
        ClassLoader::$extraOptionHandlers = array();
        parent::tearDown();
    }

    /**
     * Provides options with and without a class param
     * @return array of args
     */
    public function dataFortestSetClass()
    {
        return array(
            array(
                array(
                    'class' => 'Cascade\Tests\Fixtures\SampleClass',
                    'some_param' => 'abc'
                ),
                'Cascade\Tests\Fixtures\SampleClass'
            ),
            array(
                array(
                    'some_param' => 'abc'
                ),
                '\stdClass'
            )
        );
    }

    /**
     * Testing the setClass method
     *
     * @param  array $options Array of options
     * @param  string $expectedClass Expected classname of the instantiated object
     * @dataProvider dataFortestSetClass
     */
    public function testSetClass($options, $expectedClass)
    {
        $loader = new ClassLoader($options);

        $this->assertEquals($expectedClass, $loader->class);
    }

    public function testOptionsToCamelCase()
    {
        $array = array('hello_there' => 'Hello', 'bye_bye' => 'Bye');

        $this->assertEquals(
            array('helloThere' => 'Hello', 'byeBye' => 'Bye'),
            ClassLoader::optionsToCamelCase($array)
        );
    }

    public function testGetExtraOptionsHandler()
    {
        ClassLoader::$extraOptionHandlers = array(
            '*' => array(
                'hello' => function ($instance, $value) {
                    $instance->setHello(strtoupper($value));
                }
            ),
            'Cascade\Tests\Fixtures\SampleClass' => array(
                'there' => function ($instance, $value) {
                    $instance->setThere(strtoupper($value).'!!!');
                }
            )
        );

        $loader = new ClassLoader(array());
        $existingHandler = $loader->getExtraOptionsHandler('hello');
        $this->assertNotNull($existingHandler);
        $this->assertTrue(is_callable($existingHandler));

        $this->assertNull($loader->getExtraOptionsHandler('nohandler'));
    }

    public function testLoad()
    {
        $options = array(
            'class' => 'Cascade\Tests\Fixtures\SampleClass',
            'mandatory' => 'someValue',
            'optional_X' => 'testing some stuff',
            'optional_Y' => 'testing other stuff',
            'hello' => 'hello',
            'there' => 'there',
        );

        ClassLoader::$extraOptionHandlers = array(
            '*' => array(
                'hello' => function ($instance, $value) {
                    $instance->setHello(strtoupper($value));
                }
            ),
            'Cascade\Tests\Fixtures\SampleClass' => array(
                'there' => function ($instance, $value) {
                    $instance->setThere(strtoupper($value).'!!!');
                }
            )
        );

        $loader = new ClassLoader($options);
        $instance = $loader->load();

        $expectedInstance = new SampleClass('someValue');
        $expectedInstance->optionalX('testing some stuff');
        $expectedInstance->optionalY = 'testing other stuff';
        $expectedInstance->setHello('HELLO');
        $expectedInstance->setThere('THERE!!!');

        $this->assertEquals($expectedInstance, $instance);
    }

    /**
     * Test a nested class to load
     */
    public function testLoadDependency()
    {
        $options = array(
            'class' => 'Cascade\Tests\Fixtures\DependentClass',
            'dependency' => array(
                'class' => 'Cascade\Tests\Fixtures\SampleClass',
                'mandatory' => 'someValue',
            )
        );

        $loader = new ClassLoader($options);
        $instance = $loader->load();

        $expectedInstance = new DependentClass(
            new SampleClass('someValue')
        );

        $this->assertEquals($expectedInstance, $instance);
    }
}
