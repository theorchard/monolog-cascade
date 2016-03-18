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

use Symfony\Component\Config\Loader\FileLoader;

/**
 * Abstract class that reads input from various sources: file, string or array
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
abstract class FileLoaderAbstract extends FileLoader
{
    public static $validExtensions = array();

    /**
     * Read from a file or string
     *
     * @throws \RuntimeException if the file is not readable
     *
     * @param  string $input Filepath or string
     *
     * @return string Return a string from read file or directly from $input
     */
    public function readFrom($input)
    {
        if ($this->isFile($input)) {
            if (is_readable($input) === false) {
                throw new \RuntimeException(
                    sprintf('Unable to parse "%s" as the file is not readable.', $input)
                );
            }

            // $input is a filepath, so we load that file
            $input = file_get_contents($input);
        }

        return trim($input);
    }

    /**
     * Test if a given resource is a file name or a file path
     *
     * @param  string $resource Plain string or file path
     *
     * @return boolean Whether or not the resource is a file
     */
    public function isFile($resource)
    {
        return (strpos($resource, "\n") === false) && is_file($resource);
    }

    /**
     * Validate a file extension against a list of provided valid extensions
     *
     * @param  string $filepath file path of the file we want to check
     *
     * @return boolean Whether or not the extension is valid
     */
    public function validateExtension($filepath)
    {
        return in_array(pathinfo($filepath, PATHINFO_EXTENSION), static::$validExtensions, true);
    }

    /**
     * Return a section of an array based on the key passed in
     *
     * @param  array $array Array we want the section from
     * @param  string $section Section name (key)
     *
     * @return array|mixed Return the section of an array or just a value
     */
    public function getSectionOf($array, $section = '')
    {
        if (!empty($section) && array_key_exists($section, $array)) {
            return $array[$section];
        }

        return $array;
    }
}
