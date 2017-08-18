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
namespace Cascade\Tests\Config\Loader\ClassLoader\Resolver;

use Cascade\Config\Loader\ClassLoader\Resolver\ExtraOptionsResolver;

use Symfony;

/**
 * Class ExtraOptionsResolverTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class ExtraOptionsResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reflection class for which you want to resolve extra options
     * @var \ReflectionClass
     */
    protected $reflected = null;

    /**
     * ExtraOptions Resolver
     * @var ExtraOptionsResolver
     */
    protected $resolver = null;

    /**
     * Set up function
     */
    public function setUp()
    {
        $this->class = 'Cascade\Tests\Fixtures\SampleClass';
        $this->params = array('optionalA', 'optionalB');
        $this->resolver = new ExtraOptionsResolver(
            new \ReflectionClass($this->class),
            $this->params
        );
        parent::setUp();
    }

    /**
     * Tear down function
     */
    public function tearDown()
    {
        $this->resolver = null;
        $this->class = null;
        parent::tearDown();
    }

    /**
     * Test the hsah key generation
     */
    public function testGenerateParamsHashKey()
    {
        $a = array('optionA', 'optionB', 'optionC');
        $b = array('optionA', 'optionB', 'optionC');

        $this->assertEquals(
            ExtraOptionsResolver::generateParamsHashKey($a),
            ExtraOptionsResolver::generateParamsHashKey($b)
        );
    }

    /**
     * Test the resolver contructor
     */
    public function testConstructor()
    {
        $this->assertEquals($this->class, $this->resolver->getReflected()->getName());
        $this->assertEquals($this->params, $this->resolver->getParams());
    }

    /**
     * Test resolving with valid options
     */
    public function testResolve()
    {
        $this->assertEquals(
            array_combine($this->params, array('hello', 'there')),
            $this->resolver->resolve(array('optionalB' => 'there', 'optionalA' => 'hello'))
        );

        // Resolve an empty array (edge case)
        $this->assertEquals(array(), $this->resolver->resolve(array()));
    }

    /**
     * Data provider for testResolveWithInvalidOptions
     *
     * The order of the input options does not matter and is somewhat random. The resolution
     * should reconcile those options and match them up with the closure param position
     *
     * @return array of arrays with expected resolved values and options used as input
     */
    public function optionsProvider()
    {
        return array(
            array(
                array('optionalA', 'optionalB', 'mandatory'),
                $this->getMockBuilder('Cascade\Config\Loader\ClassLoader')
                    ->disableOriginalConstructor()
                    ->getMock()->method('canHandle')
                    ->willReturn(true)
            )
        );
    }

    /**
     * Test resolving with valid options
     */
    public function testResolveWithCustomOptionHandler()
    {
        $this->params = array('optionalA', 'optionalB', 'mandatory');
        $this->resolver = new ExtraOptionsResolver(
            new \ReflectionClass($this->class),
            $this->params
        );

        // Create a stub for the SomeClass class.
        $stub = $this->getMockBuilder('Cascade\Config\Loader\ClassLoader')
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('canHandle')
            ->willReturn(true);

        // Resolve an empty array (edge case)
        $this->assertEquals(array('mandatory' => 'abc'), $this->resolver->resolve(array('mandatory' => 'abc'), $stub));
    }

    /**
     * Data provider for testResolveWithInvalidOptions
     *
     * The order of the input options does not matter and is somewhat random. The resolution
     * should reconcile those options and match them up with the closure param position
     *
     * @return array of arrays with expected resolved values and options used as input
     */
    public function invalidOptionsProvider()
    {
        return array(
            array(
                array( // Some invalid
                    'optionalB' => 'there',
                    'optionalA' => 'hello',
                    'additionalInvalid' => 'some unknow param'
                ),
                array( // All invalid
                    'someInvalidOptionA' => 'abc',
                    'someInvalidOptionB' => 'def'
                )
            )
        );
    }

    /**
     * Test resolving with invalid options. It should throw an exception.
     *
     * @param  array $invalidOptions Array of invalid options
     * @dataProvider invalidOptionsProvider
     * @expectedException Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    public function testResolveWithInvalidOptions($invalidOptions)
    {
        $this->resolver->resolve($invalidOptions);
    }
}
