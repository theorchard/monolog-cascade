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
 * Class DependentClass
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 * @author Dom Morgan <dom@d3r.com>
 */
class DependentClass
{
    /**
     * An object dependency
     * @var SampleClass
     */
    private $dependency;

    /**
     * Constructor
     *
     * @param SampleClass $dependency Some sample object
     */
    public function __construct(SampleClass $dependency)
    {
        $this->setDependency($dependency);
    }

    /**
     * Set the object dependency
     *
     * @param SampleClass $dependency Some sample object
     */
    public function setDependency(SampleClass $dependency)
    {
        $this->dependency = $dependency;
    }
}
