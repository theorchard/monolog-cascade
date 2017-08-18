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

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\LogglyHandler;

use Cascade\Config\Loader\ClassLoader;

/**
 * Handler Loader. Loads the Handler options, validate them and instantiates
 * a Handler object (implementing Monolog\Handler\HandlerInterface) with all
 * the corresponding options
 * @see ClassLoader
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class HandlerLoader extends ClassLoader
{
    /**
     * Default handler class to use if none is provided in the option array
     */
    const DEFAULT_CLASS = 'Monolog\Handler\StreamHandler';

    /**
     * Constructor
     * @see ClassLoader::__construct
     * @see \Monolog\Handler classes for handler options
     *
     * @param array $handlerOptions Handler options
     * @param FormatterInterface[] $formatters Array of formatter to pick from
     * @param callable[] $processors Array of processors to pick from
     * @param callable[] $handlers Array of handlers to pick from
     */
    public function __construct(
        array &$handlerOptions,
        array $formatters = array(),
        array $processors = array(),
        array $handlers = array()
    ) {
        $this->populateFormatters($handlerOptions, $formatters);
        $this->populateProcessors($handlerOptions, $processors);
        $this->populateHandlers($handlerOptions, $handlers);
        parent::__construct($handlerOptions);

        self::initExtraOptionsHandlers();
    }

    /**
     * Replace the formatter name in the option array with the corresponding object from the
     * formatter array passed in if it exists.
     *
     * If no formatter is specified in the options, Monolog will use its default formatter for the
     * handler
     *
     * @throws \InvalidArgumentException
     *
     * @param  array &$handlerOptions Handler options
     * @param  FormatterInterface[] $formatters Array of formatter to pick from
     */
    private function populateFormatters(array &$handlerOptions, array $formatters)
    {
        if (isset($handlerOptions['formatter'])) {
            if (isset($formatters[$handlerOptions['formatter']])) {
                $handlerOptions['formatter'] = $formatters[$handlerOptions['formatter']];
            } else {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Formatter %s not found in the configured formatters',
                        $handlerOptions['formatter']
                    )
                );
            }
        }
    }

    /**
     * Replace the processors in the option array with the corresponding callable from the
     * array of loaded and callable processors, if it exists.
     *
     * @throws \InvalidArgumentException
     *
     * @param  array &$handlerOptions Handler options
     * @param  callable[] $processors Array of processors to pick from
     */
    private function populateProcessors(array &$handlerOptions, array $processors)
    {
        $processorArray = array();

        if (isset($handlerOptions['processors'])) {
            foreach ($handlerOptions['processors'] as $processorId) {
                if (isset($processors[$processorId])) {
                    $processorArray[] = $processors[$processorId];
                } else {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Cannot add processor "%s" to the handler. Processor not found.',
                            $processorId
                        )
                    );
                }
            }

            $handlerOptions['processors'] = $processorArray;
        }
    }

     /**
     * Replace the handler or handlers in the option array with the corresponding callable(s) from the
     * array of loaded and callable handlers, if they exist.
     *
     * @throws \InvalidArgumentException
     *
     * @param  array &$handlerOptions Handler options
     * @param  callable[] $handlers Array of handlers to pick from
     */
    private function populateHandlers(array &$handlerOptions, array $handlers)
    {
        $handlerArray = array();

        if (isset($handlerOptions['handlers'])) {
            foreach ($handlerOptions['handlers'] as $handlerId) {
                if (isset($handlers[$handlerId])) {
                    $handlerArray[] = $handlers[$handlerId];
                } else {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Cannot add handler "%s" to the handler. Handler not found.',
                            $handlerId
                        )
                    );
                }
            }

            $handlerOptions['handlers'] = $handlerArray;
        }

        if (isset($handlerOptions['handler'])) {
            $handlerId = $handlerOptions['handler'];

            if (isset($handlers[$handlerId])) {
                $handlerOptions['handler'] = $handlers[$handlerId];
            } else {
                 throw new \InvalidArgumentException(
                     sprintf(
                         'Cannot add handler "%s" to the handler. Handler not found.',
                         $handlerId
                     )
                 );
            }
        }
    }

    /**
     * Loads the closures as option handlers. Add handlers to this function if
     * you want to support additional custom options.
     *
     * The syntax is the following:
     *     array(
     *         '\Full\Absolute\Namespace\ClassName' => array(
     *             'myOption' => Closure
     *         ), ...
     *     )
     *
     * You can use the '*' wildcard if you want to set up an option for all
     * Handler classes
     */
    public static function initExtraOptionsHandlers()
    {
        self::$extraOptionHandlers = array(
            '*' => array(
                'formatter' => function (HandlerInterface $instance, FormatterInterface $formatter) {
                    $instance->setFormatter($formatter);
                },
                'processors' => function (HandlerInterface $instance, array $processors) {
                    // We need to reverse the array because Monolog "pushes" processors to top of the stack
                    foreach (array_reverse($processors) as $processor) {
                        $instance->pushProcessor($processor);
                    }
                }
            ),
            'Monolog\Handler\LogglyHandler' => array(
                'tags' => function (LogglyHandler $instance, $tags) {
                    $instance->setTag($tags);
                }
            )
        );
    }
}
