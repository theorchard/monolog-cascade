<?php
/**
 * This file is part of the Monolog Cascade package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cascade\Config\Loader\FileLoader;

/**
 * PhpArray loader class. Loads a file that returns a PHP array.
 *
 * @see FileLoaderAbstract
 */
class PhpArray extends FileLoaderAbstract
{
    /**
     * Valid file extensions for this loader
     *
     * @var array
     */
    public static $validExtensions = array('php');

    /**
     * Load a PHP file
     *
     * @param  string $resource File path to a PHP file that returns an array
     * @param  string|null $type This is not used
     *
     * @return array Array containing data from the PHP file
     */
    public function load($resource, $type = null)
    {
        $config = include $resource;

        if (!is_array($config)) {
            throw new \InvalidArgumentException(
                sprintf('The file "%s" did not return a valid PHP array when included', $resource)
            );
        }

        return $config;
    }

    /**
     * Return whether or not the resource passed in is supported by this loader
     * /!\ This does not verify that the php file returns a valid array. An exception
     * will be thrown when it is loaded if that is not the case.
     *
     * @param  string $resource Filepath
     * @param  string $type Not used
     *
     * @return boolean Whether or not the passed in resource is supported by this loader
     */
    public function supports($resource, $type = null)
    {
        return $this->isFile($resource) && $this->validateExtension($resource);
    }
}
