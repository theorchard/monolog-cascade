<?php
/*
 * This file is part of the Monolog Cascade package.
 *
 * (c) Raphael Antonmattei <rantonmattei@theorchard.com>
 * (c) The Orchard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cascade\Config\Loader;

use Cascade\Config\Loader\ClassLoader\Resolver\ConstructorResolver;
use Cascade\Config\Loader\ClassLoader\Resolver\ExtraOptionsResolver;
use Cascade\Util;

/**
 * Class Loader. Instantiate an object given a set of options. The option might look like:
 *     array(
 *         'class' => 'Some\Class'
 *         'some_contruct_param' => 'abc',
 *         'some_param' => 'def',
 *         'some_other_param' => 'sdsad'
 *     )
 *
 * Some of them are applicable to the constructor, other are applicable to other handlers.
 * For the latter you need to make sure there is a handler defined for that option
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 * @author Dom Morgan <dom@d3r.com>
 */
class ClassLoader
{
    /**
     * Default class to use if none is provided in the option array
     */
    const DEFAULT_CLASS = '\stdClass';

    /**
     * Array of Closures indexed by class.
     *     array(
     *         '\Full\Absolute\Namespace\ClassName' => array(
     *             'myOption' => Closure
     *         ), ...
     *     )
     * @var array
     */
    public static $extraOptionHandlers = array();

    /**
     * Name of the class you want to load
     * @var String
     */
    public $class = null;

    /**
     * Reflected object of the class passed in
     * @var \ReflectionClass
     */
    protected $reflected = null;

    /**
     * Array of options. This is a raw copy of the options passed in.
     * @var array
     */
    protected $rawOptions = array();

    /**
     * Constructor
     *
     * @param array $options Array of options
     * The option array might look like:
     *     array(
     *         'class' => 'Some\Class',
     *         'some_contruct_param' => 'abc',
     *         'some_param' => 'def',
     *         'some_other_param' => 'sdsad'
     *     )
     */
    public function __construct(array $options)
    {
        $this->rawOptions = self::optionsToCamelCase($options);
        $this->setClass();
        $this->reflected = new \ReflectionClass($this->class);
    }

    /**
     * Set the class you want to load from the raw option array
     */
    protected function setClass()
    {
        if (!isset($this->rawOptions['class'])) {
            $this->rawOptions['class'] = static::DEFAULT_CLASS;
        }

        $this->class = $this->rawOptions['class'];
        unset($this->rawOptions['class']);
    }

    /**
     * Recursively loads objects into any of the rawOptions that represent
     * a class
     */
    protected function loadChildClasses()
    {
        foreach ($this->rawOptions as &$option) {
            if (is_array($option)
                && array_key_exists('class', $option)
                && class_exists($option['class'])
            ) {
                $classLoader = new ClassLoader($option);
                $option = $classLoader->load();
            }
        }
    }

    /**
     * Return option values indexed by name using camelCased keys
     *
     * @param  array  $options Array of options
     *
     * @return mixed[] Array of options indexed by (camelCased) name
     */
    public static function optionsToCamelCase(array $options)
    {
        $optionsByName = array();

        if (count($options)) {
            foreach ($options as $name => $value) {
                $optionsByName[Util::snakeToCamelCase($name)] = $value;
            }
        }

        return $optionsByName;
    }

    /**
     * Resolve options and returns them into 2 buckets:
     *   - constructor options and
     *   - extra options
     * Extra options are those that are not in the contructor. The constructor arguments determine
     * what goes into which bucket
     *
     * @return array Array of constructorOptions and extraOptions
     */
    private function resolveOptions()
    {
        $constructorResolver = new ConstructorResolver($this->reflected);

        // Contructor options are only the ones matching the contructor args' names
        $constructorOptions = array_intersect_key(
            $this->rawOptions,
            $constructorResolver->getConstructorArgs()
        );

        // Extra options are everything else than contructor options
        $extraOptions = array_diff_key(
            $this->rawOptions,
            $constructorOptions
        );

        $extraOptionsResolver = new ExtraOptionsResolver(
            $this->reflected,
            array_keys($extraOptions)
        );

        return array(
            $constructorResolver->resolve($constructorOptions),
            $extraOptionsResolver->resolve($extraOptions, $this)
        );
    }

    /**
     * Instantiate the reflected object using the parsed contructor args and set
     * extra options if any
     *
     * @return mixed Instance of the reflected object
     */
    public function load()
    {
        $this->loadChildClasses();

        list($constructorResolvedOptions, $extraResolvedOptions) = $this->resolveOptions();
        $instance = $this->reflected->newInstanceArgs($constructorResolvedOptions);

        $this->loadExtraOptions($extraResolvedOptions, $instance);

        return $instance;
    }

    /**
     * Indicates whether or not an option is supported by the loader
     *
     * @param  string $extraOptionName Option name
     *
     * @return boolean Whether or not an option is supported by the loader
     */
    public function canHandle($extraOptionName)
    {
        return
            isset(self::$extraOptionHandlers['*'][$extraOptionName]) ||
            isset(self::$extraOptionHandlers[$this->class][$extraOptionName]);
    }

    /**
     * Get the corresponding handler for a given option
     *
     * @param  string $extraOptionName Option name
     *
     * @return \Closure|null Corresponding Closure object or null if not found
     */
    public function getExtraOptionsHandler($extraOptionName)
    {
        // Check extraOption handlers that are valid for all classes
        if (isset(self::$extraOptionHandlers['*'][$extraOptionName])) {
            return self::$extraOptionHandlers['*'][$extraOptionName];
        }

        // Check extraOption handlers that are valid for the given class
        if (isset(self::$extraOptionHandlers[$this->class][$extraOptionName])) {
            return self::$extraOptionHandlers[$this->class][$extraOptionName];
        }

        return null;
    }

    /**
     * Set extra options if any were requested
     *
     * @param  array $extraOptions Array of extra options (key => value)
     * @param  mixed $instance Instance you want to set options for
     */
    public function loadExtraOptions($extraOptions, $instance)
    {
        foreach ($extraOptions as $name => $value) {
            if ($this->reflected->hasMethod($name)) {
                // There is a method to handle this option
                call_user_func_array(
                    array($instance, $name),
                    is_array($value) ? $value : array($value)
                );
                continue;
            }
            if ($this->reflected->hasProperty($name) &&
                $this->reflected->getProperty($name)->isPublic()
            ) {
                // There is a public member we can set for this option
                $instance->$name = $value;
                continue;
            }

            if ($this->canHandle($name)) {
                // There is a custom handler for that option
                $closure = $this->getExtraOptionsHandler($name);
                $closure($instance, $value);
            }
        }
    }
}
