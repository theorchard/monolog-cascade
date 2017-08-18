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

use Monolog;

use Cascade\Config\Loader\ClassLoader;

/**
 * Formatter Loader. Loads the Formatter options, validate them and instantiates
 * a Formatter object (implementing Monolog\Formatter\FormatterInterface) with all
 * the corresponding options
 * @see ClassLoader
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class FormatterLoader extends ClassLoader
{
    /**
     * Default formatter class to use if none is provided in the option array
     */
    const DEFAULT_CLASS = 'Monolog\Formatter\LineFormatter';

    /**
     * Constructor
     * @see ClassLoader::__construct
     * @see Monolog\Formatter classes for formatter options
     *
     * @param array $formatterOptions Formatter options
     */
    public function __construct(array $formatterOptions)
    {
        parent::__construct($formatterOptions);

        self::initExtraOptionsHandlers();
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
     * Formatter classes
     *
     * @todo add handlers to handle extra options for all known Monolog formatters
     */
    public static function initExtraOptionsHandlers()
    {
        self::$extraOptionHandlers = array(
            'Monolog\Formatter\LineFormatter' => array(
                'includeStacktraces' => function (Monolog\Formatter\LineFormatter $instance, $include) {
                    $instance->includeStacktraces($include);
                }
            )
        );
    }
}
