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
namespace Cascade\Config\Loader\ClassLoader;

use Cascade\Cascade;

use Monolog;

/**
 * Logger Loader. Instantiate a Logger and set passed in handlers and processors if any
 * @see ClassLoader
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class LoggerLoader
{
    /**
     * Array of options
     * @var array
     */
    protected $loggerOptions = array();

    /**
     * Array of handlers
     * @var Monolog\Handler\HandlerInterface[]
     */
    protected $handlers = array();

    /**
     * Array of processors
     * @var callable[]
     */
    protected $processors = array();

    /**
     * Logger
     * @var Monolog\Logger
     */
    protected $logger = null;

    /**
     * Constructor
     *
     * @param string $loggerName Name of the logger
     * @param array  $loggerOptions Array of logger options
     * @param Monolog\Handler\HandlerInterface[] $handlers Array of Monolog handlers
     * @param callable[] $processors Array of processors
     */
    public function __construct(
        $loggerName,
        array $loggerOptions = array(),
        array $handlers = array(),
        array $processors = array()
    ) {
        $this->loggerOptions = $loggerOptions;
        $this->handlers = $handlers;
        $this->processors = $processors;

        // This instantiates a Logger object and set it to the Registry
        $this->logger = Cascade::getLogger($loggerName);
    }

    /**
     * Resolve handlers for that Logger (if any provided) against an array of previously set
     * up handlers. Returns an array of valid handlers.
     *
     * @throws \InvalidArgumentException if a requested handler is not available in $handlers
     *
     * @param  array $loggerOptions Array of logger options
     * @param  Monolog\Handler\HandlerInterface[] $handlers Available Handlers to resolve against
     *
     * @return Monolog\Handler\HandlerInterface[] Array of Monolog handlers
     */
    public function resolveHandlers(array $loggerOptions, array $handlers)
    {
        $handlerArray = array();

        if (isset($loggerOptions['handlers'])) {
            // If handlers have been specified and, they do exist in the provided handlers array
            // We return an array of handler objects
            foreach ($loggerOptions['handlers'] as $handlerId) {
                if (isset($handlers[$handlerId])) {
                    $handlerArray[] = $handlers[$handlerId];
                } else {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Cannot add handler "%s" to the logger "%s". Handler not found.',
                            $handlerId,
                            $this->logger->getName()
                        )
                    );
                }
            }
        }

        // If nothing is set there is nothing to resolve, Handlers will be Monolog's default

        return $handlerArray;
    }

    /**
     * Resolve processors for that Logger (if any provided) against an array of previously set
     * up processors.
     *
     * @throws \InvalidArgumentException if a requested processor is not available in $processors
     *
     * @param  array $loggerOptions Array of logger options
     * @param  callable[] $processors Available Processors to resolve against
     *
     * @return callable[] Array of Monolog processors
     */
    public function resolveProcessors(array $loggerOptions, $processors)
    {
        $processorArray = array();

        if (isset($loggerOptions['processors'])) {
            // If processors have been specified and, they do exist in the provided processors array
            // We return an array of processor objects
            foreach ($loggerOptions['processors'] as $processorId) {
                if (isset($processors[$processorId])) {
                    $processorArray[] = $processors[$processorId];
                } else {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Cannot add processor "%s" to the logger "%s". Processor not found.',
                            $processorId,
                            $this->logger->getName()
                        )
                    );
                }
            }
        }

        // If nothing is set there is nothing to resolve, Processors will be Monolog's default

        return $processorArray;
    }

    /**
     * Add handlers to the Logger
     *
     * @param Monolog\Handler\HandlerInterface[] Array of Monolog handlers
     */
    private function addHandlers(array $handlers)
    {
        // We need to reverse the array because Monolog "pushes" handlers to top of the stack
        foreach (array_reverse($handlers) as $handler) {
            $this->logger->pushHandler($handler);
        }
    }

    /**
     * Add processors to the Logger
     *
     * @param callable[] Array of Monolog processors
     */
    private function addProcessors(array $processors)
    {
        // We need to reverse the array because Monolog "pushes" processors to top of the stack
        foreach (array_reverse($processors) as $processor) {
            $this->logger->pushProcessor($processor);
        }
    }

    /**
     * Return the instantiated Logger object based on its name
     *
     * @return Monolog\Logger Logger object
     */
    public function load()
    {
        $this->addHandlers($this->resolveHandlers($this->loggerOptions, $this->handlers));
        $this->addProcessors($this->resolveProcessors($this->loggerOptions, $this->processors));

        return $this->logger;
    }
}
