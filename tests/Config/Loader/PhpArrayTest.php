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

use Cascade\Config\Loader\PhpArray as ArrayLoader;
use Cascade\Tests\Fixtures;

/**
 * Class PhpArrayTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class PhpArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Array loader object
     * @var ArrayLoader
     */
    protected $arrayLoader = null;

    public function setUp()
    {
        parent::setUp();

        $this->arrayLoader = new ArrayLoader();
    }

    public function tearDown()
    {
        $this->arrayLoader = null;
        parent::tearDown();
    }

    /**
     * Test loading a Php array
     */
    public function testLoad()
    {
        $array = Fixtures::getSamplePhpArray();
        $this->assertSame($array, $this->arrayLoader->load($array));
    }

    /**
     * Data provider for testSupportsWithInvalidResource
     *
     * @return array array of non-array values
     */
    public function notStringDataProvider()
    {
        return array(
            array('Some string'),
            array(true),
            array(123),
            array(123.456),
            array(null),
            array(new \stdClass),
            array(function () {
            })
        );
    }

    /**
     * Test loading resources supported by the YamlLoader
     *
     * @param mixed $invalidResource Invalid resource value
     * @dataProvider notStringDataProvider
     */
    public function testSupportsWithInvalidResource($invalidResource)
    {
        $this->assertFalse($this->arrayLoader->supports($invalidResource));
    }

    /**
     * Test supports with a valid array
     */
    public function testSupports()
    {
        $array = Fixtures::getSamplePhpArray();
        $this->assertTrue($this->arrayLoader->supports($array));
    }
}
