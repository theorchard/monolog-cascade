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
namespace Cascade\Config\Loader\FileLoader;

use Symfony\Component\Yaml\Yaml as YamlParser;

/**
 * Yaml loader class. It can load a Yaml string or a Yaml file
 * @see FileLoaderAbstract
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class Yaml extends FileLoaderAbstract
{
    /**
     * Valid file extensions for this loader
     * @var array
     */
    public static $validExtensions = array(
        'yaml', // official extension
        'yml'   // but everybody uses that one
    );

    /**
     * Load a Yaml string/file
     *
     * @param  string $resource Yaml string or file path to a Yaml file
     * @param  string|null $type Not used
     *
     * @return array Array containing data from the parse Yaml string or file
     */
    public function load($resource, $type = null)
    {
        return YamlParser::parse($this->readFrom($resource));
    }

    /**
     * Return whether or not the resource passed in is supported by this loader
     * /!\ This does not validate Yaml content. The parser will raise an exception in that case
     *
     * @param  string $resource Plain string or filepath
     * @param  string $type Not used
     *
     * @return boolean Whether or not the passed in resrouce is supported by this loader
     */
    public function supports($resource, $type = null)
    {
        if (!is_string($resource)) {
            return false;
        }

        if ($this->isFile($resource)) {
            return $this->validateExtension($resource);
        }

        return true;
    }
}
