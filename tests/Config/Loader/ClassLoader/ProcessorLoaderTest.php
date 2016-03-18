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

use Monolog\Processor\WebProcessor;

use Cascade\Config\Loader\ClassLoader\ProcessorLoader;

/**
 * Class ProcessorLoaderTest
 *
 * @author Kate Burdon <kburdon@tableau.com>
 */
class ProcessorLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessorLoader()
    {
        $options = array(
            'class' => 'Monolog\Processor\WebProcessor'
        );
        $processors = array(new WebProcessor());
        $loader = new ProcessorLoader($options, $processors);

        $this->assertEquals($loader->class, $options['class']);
    }
}
