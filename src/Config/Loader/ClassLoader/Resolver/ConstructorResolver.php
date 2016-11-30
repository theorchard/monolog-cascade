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
namespace Cascade\Config\Loader\ClassLoader\Resolver;

use Cascade\Util;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Constructor Resolver. Pull args from the contructor and set up an option
 * resolver against args requirements
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class ConstructorResolver
{
    /**
     * Reflection class for which you want to resolve constructor options
     * @var \ReflectionClass
     */
    protected $reflected = null;

    /**
     * Registry of resolvers
     * @var array
     */
    private static $resolvers = array();

    /**
     * Associative array of contructor args to resolve against
     * @var \ReflectionParameter[]
     */
    protected $constructorArgs = array();

    /**
     * Contructor
     *
     * @param \ReflectionClass $reflected Reflection class for which you want to resolve
     * constructor options
     */
    public function __construct(\ReflectionClass $reflected)
    {
        $this->reflected = $reflected;
        $this->initConstructorArgs();
    }

    /**
     * Fetches constructor args (array of ReflectionParameter) from the reflected class
     * and set them as an associative array
     *
     * Convert the parameter names to camelCase for classes that have contructor
     * params defined in snake_case for consistency with the options
     */
    public function initConstructorArgs()
    {
        $constructor = $this->reflected->getConstructor();

        if (!is_null($constructor)) {
            // Index parameters by their names
            foreach ($constructor->getParameters() as $param) {
                $name = Util::snakeToCamelCase($param->getName());
                $this->constructorArgs[$name] = $param;
            }
        }
    }

    /**
     * Returns the contructor args as an associative array
     *
     * @return array Contructor args
     */
    public function getConstructorArgs()
    {
        return $this->constructorArgs;
    }

    /**
     * Returns the reflected object
     *
     * @return \ReflectionClass
     */
    public function getReflected()
    {
        return $this->reflected;
    }

    /**
     * Configure options for the provided OptionResolver to match contructor args requirements
     *
     * @param  OptionsResolver $optionsResolver OptionResolver to configure
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        foreach ($this->constructorArgs as $name => $param) {
            if ($param->isOptional() && $param->isDefaultValueAvailable()) {
                $optionsResolver->setDefault($name, $param->getDefaultValue());
            } else {
                $optionsResolver->setRequired($name);
            }
        }
    }

    /**
     * Loops through constructor args and buid an ordered array of args using
     * the option values passed in. We assume the passed in array has been resolved already.
     * i.e. That the arg name has an entry in the option array.
     *
     * @param  array $hashOfOptions Array of options
     *
     * @return array Array of ordered args
     */
    public function hashToArgsArray($hashOfOptions)
    {
        $optionsArray = new \SplFixedArray(count($hashOfOptions));

        foreach ($this->constructorArgs as $name => $param) {
            $optionsArray[$param->getPosition()] = $hashOfOptions[$name];
        }

        return $optionsArray->toArray();
    }

    /**
     * Resolve options against constructor args
     *
     * @param  array $options Array of option values. Expected array looks like:
     *     array(
     *         'someParam' => 'def',
     *         'someOtherParam' => 'sdsad'
     *     )
     *
     * @return array Array of resolved ordered args
     */
    public function resolve(array $options)
    {
        $reflectedClassName = $this->reflected->getName();

        // We check if that constructor has been configured before and is in the registry
        if (!isset(self::$resolvers[$reflectedClassName])) {
            self::$resolvers[$reflectedClassName] = new OptionsResolver();

            $this->configureOptions(self::$resolvers[$reflectedClassName]);
        }

        return $this->hashToArgsArray(
            self::$resolvers[$reflectedClassName]->resolve($options)
        );
    }
}
