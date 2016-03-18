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
namespace Cascade\Config\Loader;

use Symfony\Component\Config\Loader\Loader;

/**
 * Array loader class. It loads a php array
 * @see Loader
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class PhpArray extends Loader
{
    /**
     * Loads an array
     *
     * @param  array $array Array to load
     * @param  string|null $type Not used
     *
     * @return array The passed in array
     */
    public function load($array, $type = null)
    {
        return $array;
    }

    /**
     * Return whether or not the passed in resource is supported by this loader
     *
     * @param  string $resource Plain string or filepath
     * @param  string|null $type Not used
     *
     * @return boolean Whether or not the passed in resource is supported by this loader
     */
    public function supports($resource, $type = null)
    {
        return is_array($resource);
    }
}
