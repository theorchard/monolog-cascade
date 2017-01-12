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
namespace Cascade;

use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Monolog\Registry;

use Cascade\Config\ConfigLoader;

/**
 * Module class that manages Monolog Logger object
 * @see Logger
 * @see Registry
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class Cascade
{
    /**
     * Config class that holds options for all registered loggers
     * This is optional, you can set up your loggers programmatically
     * @var Config
     */
    protected static $config = null;

    /**
     * Create a new Logger object and push it to the registry
     * @see Logger::__construct
     *
     * @throws \InvalidArgumentException if no name is given
     *
     * @param string $name The logging channel
     * @param HandlerInterface[] $handlers Optional stack of handlers, the first
     * one in the array is called first, etc.
     * @param callable[] $processors Optional array of processors
     *
     * @return Logger Newly created Logger
     */
    public static function createLogger(
        $name,
        array $handlers = array(),
        array $processors = array()
    ) {

        if (empty($name)) {
            throw new \InvalidArgumentException('Logger name is required.');
        }

        $logger = new Logger($name, $handlers, $processors);
        Registry::addLogger($logger);

        return $logger;
    }

    /**
     * Get a Logger instance by name. Creates a new one if a Logger with the
     * provided name does not exist
     *
     * @param  string $name Name of the requested Logger instance
     *
     * @return Logger Requested instance of Logger or new instance
     */
    public static function getLogger($name)
    {
        return Registry::hasLogger($name) ? Registry::getInstance($name) : self::createLogger($name);
    }

    /**
     * Alias of getLogger
     * @see getLogger
     *
     * @param  string $name Name of the requested Logger instance
     *
     * @return Logger Requested instance of Logger or new instance
     */
    public static function logger($name)
    {
        return self::getLogger($name);
    }

    /**
     * Return the config options
     *
     * @return Config Array with configuration options
     */
    public static function getConfig()
    {
        return self::$config;
    }

    /**
     * Load configuration options from a file, a JSON or Yaml string or an array.
     *
     * @param string|array $resource Path to config file or configuration as string or array
     */
    public static function fileConfig($resource)
    {
        self::$config = new Config($resource, new ConfigLoader());
        self::$config->load();
        self::$config->configure();
    }

    /**
     * Load configuration options from a JSON or Yaml string. Alias of fileConfig.
     * @see fileConfig
     *
     * @param string $configString Configuration in string form
     */
    public static function loadConfigFromString($configString)
    {
        self::fileConfig($configString);
    }

    /**
     * Load configuration options from an array. Alias of fileConfig.
     * @see fileConfig
     *
     * @param array $configArray Configuration in array form
     */
    public static function loadConfigFromArray($configArray)
    {
        self::fileConfig($configArray);
    }
}
