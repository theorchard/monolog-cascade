<?php
namespace Cascade\Config;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;

use Cascade\Config\Loader\PhpArray as ArrayLoader;
use Cascade\Config\Loader\FileLoader\Json as JsonLoader;
use Cascade\Config\Loader\FileLoader\Yaml as YamlLoader;

/**
 * Loader class that loads Yaml, JSON and array from various resources (file, php array, string)
 * @see DelegatingLoader
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class ConfigLoader extends DelegatingLoader
{
    /**
     * Locator
     * @var Symfony\Component\Config\FileLocator
     */
    protected $locator = null;

    /**
     * Instantiate a Loader object
     * @todo: have the locator passed to the constructor so we can load more than one file
     */
    public function __construct()
    {
        $this->locator = new FileLocator();

        $loaderResolver = new LoaderResolver(array(
            // Do not change that order, it does matter as the resolver returns the first loader
            // that meets the requirements of the "supports" method for each of those loaders
            new ArrayLoader(),
            new JsonLoader($this->locator),
            new YamlLoader($this->locator)
        ));

        parent::__construct($loaderResolver);
    }

    /**
     * Loads a configuration resource: file, array, string
     *
     * @param  mixed $resource resource to load
     * @param  mixed $type not used
     * @return array array of config options
     */
    public function load($resource, $type = null)
    {
        return parent::load($resource);
    }
}
