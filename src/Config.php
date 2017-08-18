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

use Monolog;

use Cascade\Config\ConfigLoader;
use Cascade\Config\Loader\ClassLoader\FormatterLoader;
use Cascade\Config\Loader\ClassLoader\HandlerLoader;
use Cascade\Config\Loader\ClassLoader\LoggerLoader;
use Cascade\Config\Loader\ClassLoader\ProcessorLoader;

/**
 * Config class that takes a config resource (file, JSON, Yaml, etc.) and configure Loggers with
 * all the required options (Formatters, Handlers, etc.)
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class Config
{
    /**
     * Input from user. This is either a file path, a string or an array
     * @var string|array
     */
    protected $input = null;

    /**
     * Array of logger configuration options: (logger attributes, formatters, handlers, etc.)
     * @var array
     */
    protected $options = array();

    /**
     * Array of Formatter objects
     * @var Monolog\Formatter\FormatterInterface[]
     */
    protected $formatters = array();

    /**
     * Array of Handler objects
     * @var Monolog\Handler\HandlerInterface[]
     */
    protected $handlers = array();

    /**
     * Array of Processor objects
     * @var callable[]
     */
    protected $processors = array();

    /**
     * Array of logger objects
     * @var Monolog\Logger[]
     */
    protected $loggers = array();

    /**
     * Config loader
     * @var ConfigLoader
     */
    protected $loader = null;

    /**
     * Instantiate a Config object
     *
     * @param string|array $input User input
     * @param ConfigLoader $loader Config loader object
     */
    public function __construct($input, ConfigLoader $loader)
    {
        $this->input = $input;
        $this->loader = $loader;
    }

    /**
     * Load config options into the options array using the injected loader
     */
    public function load()
    {
        $this->options = $this->loader->load($this->input);
    }

    /**
     * Configure and register Logger(s) according to the options passed in
     */
    public function configure()
    {
        if (!isset($this->options['disable_existing_loggers'])) {
            // We disable any existing loggers by default
            $this->options['disable_existing_loggers'] = true;
        }

        if ($this->options['disable_existing_loggers']) {
            Monolog\Registry::clear();
        }

        if (isset($this->options['formatters'])) {
            $this->configureFormatters($this->options['formatters']);
        }

        if (isset($this->options['processors'])) {
            $this->configureProcessors($this->options['processors']);
        }

        if (isset($this->options['handlers'])) {
            $this->configureHandlers($this->options['handlers']);
        }

        if (isset($this->options['loggers'])) {
            $this->configureLoggers($this->options['loggers']);
        } else {
            throw new \RuntimeException(
                'Cannot configure loggers. No logger configuration options provided.'
            );
        }
    }

    /**
     * Configure the formatters
     *
     * @param  array $formatters Array of formatter options
     */
    protected function configureFormatters(array $formatters = array())
    {
        foreach ($formatters as $formatterId => $formatterOptions) {
            $formatterLoader = new FormatterLoader($formatterOptions);
            $this->formatters[$formatterId] = $formatterLoader->load();
        }
    }

    /**
     * Configure the handlers
     *
     * @param  array $handlers Array of handler options
     */
    protected function configureHandlers(array $handlers)
    {
        foreach ($handlers as $handlerId => $handlerOptions) {
            $handlerLoader = new HandlerLoader($handlerOptions, $this->formatters, $this->processors, $this->handlers);
            $this->handlers[$handlerId] = $handlerLoader->load();
        }
    }

    /**
     * Configure the processors
     *
     * @param  array $processors Array of processor options
     */
    protected function configureProcessors(array $processors)
    {
        foreach ($processors as $processorName => $processorOptions) {
            $processorLoader = new ProcessorLoader($processorOptions, $this->processors);
            $this->processors[$processorName] = $processorLoader->load();
        }
    }

    /**
     * Configure the loggers
     *
     * @param  array $loggers Array of logger options
     */
    protected function configureLoggers(array $loggers)
    {
        foreach ($loggers as $loggerName => $loggerOptions) {
            $loggerLoader = new LoggerLoader($loggerName, $loggerOptions, $this->handlers, $this->processors);
            $this->loggers[$loggerName] = $loggerLoader->load();
        }
    }
}
