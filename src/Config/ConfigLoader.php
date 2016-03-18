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
namespace Cascade\Config;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;

use Cascade\Config\Loader\FileLoader\Json as JsonLoader;
use Cascade\Config\Loader\FileLoader\PhpArray as ArrayFromFileLoader;
use Cascade\Config\Loader\FileLoader\Yaml as YamlLoader;
use Cascade\Config\Loader\PhpArray as ArrayLoader;

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
     * @var FileLocator
     */
    protected $locator = null;

    /**
     * Instantiate a Loader object
     * @todo have the locator passed to the constructor so we can load more than one file
     */
    public function __construct()
    {
        $this->locator = new FileLocator();

        $loaderResolver = new LoaderResolver(array(
            // Do not change that order, it does matter as the resolver returns the first loader
            // that meets the requirements of the "supports" method for each of those loaders
            new ArrayLoader(),
            new ArrayFromFileLoader($this->locator),
            new JsonLoader($this->locator),
            new YamlLoader($this->locator)
        ));

        parent::__construct($loaderResolver);
    }

    /**
     * Loads a configuration resource: file, array, string
     *
     * @param mixed $resource Resource to load
     * @param string|null $type Not used
     *
     * @return array Array of config options
     */
    public function load($resource, $type = null)
    {
        return parent::load($resource);
    }
}
