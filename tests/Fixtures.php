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
namespace Cascade\Tests;

class Fixtures
{
    /**
     * Return the fixture directory
     * @return string ficture directory
     */
    public static function fixtureDir()
    {
        return realpath(__DIR__.'/Fixtures');
    }

    /**
     * Return a path to a non existing file
     * @return string wrong file path
     */
    public static function getInvalidFile()
    {
        return 'some/non/existing/file.txt';
    }

    /**
     * Return the fixture Yaml config file
     * @return string path to yaml config file
     */
    public static function getYamlConfigFile()
    {
        return self::fixtureDir().'/fixture_config.yml';
    }

    /**
     * Return the fixture sample Yaml file
     * @return string path to a sample yaml file
     */
    public static function getSampleYamlFile()
    {
        return self::fixtureDir().'/fixture_sample.yml';
    }

    /**
     * Return the fixture sample Yaml string
     * @return string sample yaml string
     */
    public static function getSampleYamlString()
    {
        return trim(
            '---'."\n".
            'greeting: "hello"'."\n".
            'to: "you"'."\n"
        );
    }

    /**
     * Return the fixture JSON config file
     * @return string path to JSON config file
     */
    public static function getJsonConfigFile()
    {
        return self::fixtureDir().'/fixture_config.json';
    }

    /**
     * Return the fixture sample JSON file
     * @return string path to a sample JSON file
     */
    public static function getSampleJsonFile()
    {
        return self::fixtureDir().'/fixture_sample.json';
    }

    /**
     * Return the fixture sample JSON string
     * @return string sample JSON string
     */
    public static function getSampleJsonString()
    {
        return trim(
            '{'."\n".
            '    "greeting": "hello",'."\n".
            '    "to": "you"'."\n".
            '}'."\n"
        );
    }

    /**
     * Return a sample string
     * @return string sample string
     */
    public static function getSampleString()
    {
        return " some string with new \n\n lines and white spaces \n\n";
    }

    /**
     * Return a config array
     * @return array config array
     */
    public static function getPhpArrayConfig()
    {
        return require self::fixtureDir().'/fixture_config.php';
    }

    /**
     * Return a sample array
     * @return array sample array
     */
    public static function getSamplePhpArray()
    {
        return array(
            'greeting' => 'hello',
            'to' => 'you'
        );
    }
}
