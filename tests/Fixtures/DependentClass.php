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
namespace Cascade\Tests\Fixtures;

/**
 * Class SampleClass
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class DependentClass
{
    /**
     * An object dependency
     * @var Cascade\Tests\Fixtures\SampleClass
     */
    private $dependency;

    /**
     * Constructor
     *
     * @param mixed $mandatory Some mandatory param
     * @param string $optionalA Some optional param
     */
    public function __construct(
        SampleClass $dependency
    ) {
        $this->setDependency($dependency);
    }

    /**
     * Set the object dependency
     *
     * @param Cascade\Tests\Fixtures\SampleClass $dependency Some value
     */
    public function setDependency($dependency)
    {
        $this->dependency = $dependency;
    }
}
