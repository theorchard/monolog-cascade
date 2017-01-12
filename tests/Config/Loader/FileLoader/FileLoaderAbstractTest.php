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
namespace Cascade\Tests\Config\Loader\FileLoader;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\FileLocatorInterface;
use org\bovigo\vfs\vfsStream;

use Cascade\Tests\Fixtures;

/**
 * Class FileLoaderAbstractTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class FileLoaderAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Mock of extending Cascade\Config\Loader\FileLoader\FileLoaderAbstract
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mock = null;

    public function setUp()
    {
        parent::setUp();

        $fileLocatorMock = $this->getMock(
            'Symfony\Component\Config\FileLocatorInterface'
        );

        $this->mock = $this->getMockForAbstractClass(
            'Cascade\Config\Loader\FileLoader\FileLoaderAbstract',
            array($fileLocatorMock),
            'FileLoaderAbstractMockClass' // mock class name
        );

        // Setting valid extensions for tests
        \FileLoaderAbstractMockClass::$validExtensions = array('test', 'php');
    }

    public function tearDown()
    {
        $this->mock = null;
        parent::tearDown();
    }

    /**
     * Test loading config from a valid file
     */
    public function testReadFrom()
    {
        $this->assertEquals(
            Fixtures::getSampleYamlString(),
            $this->mock->readFrom(Fixtures::getSampleYamlFile())
        );
    }

    /**
     * Test loading config from a valid file
     */
    public function testLoadFileFromString()
    {
        $this->assertEquals(
            trim(Fixtures::getSampleString()),
            $this->mock->readFrom(Fixtures::getSampleString())
        );
    }

    /**
     * Data provider for testGetSectionOf
     *
     * @return array array with original value, section and expected value
     */
    public function extensionsDataProvider()
    {
        return array(
            array(true, 'hello/world.test'),
            array(true, 'hello/world.php'),
            array(false, 'hello/world.jpeg'),
            array(false, 'hello/world'),
            array(false, '')
        );
    }

    /**
     * Test validating the extension
     *
     * @param boolean $expected Expected boolean value
     * @param string $filepath Filepath to validate
     * @dataProvider extensionsDataProvider
     */
    public function testValidateExtension($expected, $filepath)
    {
        if ($expected) {
            $this->assertTrue($this->mock->validateExtension($filepath));
        } else {
            $this->assertFalse($this->mock->validateExtension($filepath));
        }
    }

    /**
     * Data provider for testGetSectionOf
     *
     * @return array array wit original value, section and expected value
     */
    public function arrayDataProvider()
    {
        return array(
            array(
                array(
                    'a' => array('aa' => 'AA', 'ab' => 'AB'),
                    'b' => array('ba' => 'BA', 'bb' => 'BB')
                ),
                'b',
                array('ba' => 'BA', 'bb' => 'BB')
            ),
            array(
                array('a' => 'A', 'b' => 'B'),
                'c',
                array('a' => 'A', 'b' => 'B'),
            ),
            array(
                array('a' => 'A', 'b' => 'B'),
                '',
                array('a' => 'A', 'b' => 'B'),
            )
        );
    }

    /**
     * Test the getSectionOf function
     *
     * @param array $array Array of options
     * @param string $section Section key
     * @param array $expected Expected array for the given section
     * @dataProvider arrayDataProvider
     */
    public function testGetSectionOf(array $array, $section, array $expected)
    {
        $this->assertSame($expected, $this->mock->getSectionOf($array, $section));
    }

    /**
     * Test loading an invalid file
     *
     * @expectedException \RuntimeException
     */
    public function testloadFileFromInvalidFile()
    {
        // mocking the file system from a 'config_dir' base dir
        $root = vfsStream::setup('config_dir');

        // Adding an unreadable file (chmod 0000)
        vfsStream::newFile('config.yml', 0000)
            ->withContent(
                "---\n".
                "hidden_config: true"
            )->at($root);

        // This will throw an exception because the file is not readable
        $this->mock->readFrom(vfsStream::url('config_dir/config.yml'));

        stream_wrapper_unregister(vfsStream::SCHEME);
    }
}
