Monolog Cascade [![Build Status](https://travis-ci.org/theorchard/monolog-cascade.svg?branch=master)](https://travis-ci.org/theorchard/monolog-cascade) [![Coverage Status](https://coveralls.io/repos/theorchard/monolog-cascade/badge.svg?branch=master)](https://coveralls.io/r/theorchard/monolog-cascade?branch=master)
===============

What is Monolog Cascade?
------------------------

Monolog Cascade is a [Monolog](https://github.com/Seldaek/monolog) extension that allows you to set up and configure multiple loggers and handlers from a single config file.

It's been inspired by the [`logging.config`](https://docs.python.org/3.4/library/logging.config.html?highlight=fileconfig#module-logging.config) Python module.


------------


Installation
------------

Add `monolog-cascade` as a requirement in your `composer.json` file or run
```sh
$ composer require theorchard/monolog-cascade
```

Note: Monolog Cascade requires PHP 5.3.9 or higher.

Usage
-----

```php
<?php
use Cascade\Cascade;

// configure your loggers
Cascade::fileConfig('path/to/some/config.yaml');

// or use php array
$config = require 'config.php';
Cascade::fileConfig($config);

```

Then just use your logger as shown below
```php
Cascade::getLogger('myLogger')->info('Well, that works!');
Cascade::getLogger('myLogger')->error('Maybe not...');
```

Configuring your loggers
------------------------
Monolog Cascade supports the following config formats:
 - Yaml
 - JSON
 - PHP File returning Array
 - PHP Array

### Configuration structure
Here is a sample Yaml config file:
```yaml

formatters:
    dashed:
        class: Monolog\Formatter\LineFormatter
        format: "%datetime%-%channel%.%level_name% - %message%\n"
handlers:
    console:
        class: Monolog\Handler\StreamHandler
        level: DEBUG
        formatter: dashed
        processors: [memory_processor]
        stream: php://stdout
    info_file_handler:
        class: Monolog\Handler\StreamHandler
        level: INFO
        formatter: dashed
        stream: ./example_info.log
processors:
    web_processor:
        class: Monolog\Processor\WebProcessor
    memory_processor:
        class: Monolog\Processor\MemoryUsageProcessor
loggers:
    myLogger:
        handlers: [console, info_file_handler]
        processors: [web_processor]
```

Here is a sample PHP config file:
```php
<?php

return array(
    'version' => 1,

    'formatters' => array(
        'spaced' => array(
            'format' => "%datetime% %channel%.%level_name%  %message%\n",
            'include_stacktraces' => true
        ),
        'dashed' => array(
            'format' => "%datetime%-%channel%.%level_name% - %message%\n"
        ),
    ),
    'handlers' => array(
        'console' => array(
            'class' => 'Monolog\Handler\StreamHandler',
            'level' => 'DEBUG',
            'formatter' => 'spaced',
            'stream' => 'php://stdout'
        ),

        'info_file_handler' => array(
            'class' => 'Monolog\Handler\StreamHandler',
            'level' => 'INFO',
            'formatter' => 'dashed',
            'stream' => './demo_info.log'
        ),

        'error_file_handler' => array(
            'class' => 'Monolog\Handler\StreamHandler',
            'level' => 'ERROR',
            'stream' => './demo_error.log',
            'formatter' => 'spaced'
        )
    ),
    'processors' => array(
        'tag_processor' => array(
            'class' => 'Monolog\Processor\TagProcessor'
        )
    ),
    'loggers' => array(
        'my_logger' => array(
            'handlers' => array('console', 'info_file_handler')
        )
    )
);
```

More information on how the Cascade config parser loads and reads the parameters:

Only the `loggers` key is required. If `formatters` and/or `handlers` are ommitted, Monolog's default will be used. `processors` is optional and if ommitted, no processors will be used. (See the "Optional Keys" section further below).

Other keys are optional and would be interpreted as described below:

- **_formatters_** - the derived associative array (from the Yaml or JSON) in which each key is the formatter identifier holds keys/values to configure your formatters.
The only _reserved_ key is `class` and it should contain the classname of the formatter you would like to use. Other parameters will be interpreted as constructor parameters for that class and passed in when the formatter object is instanced by the Cascade config loader.<br />
If some parameters are not present in the constructor, they will be treated as extra parameters and Cascade will try to interpret them should they match any custom handler functions that are able to use them. (see [Extra Parameters](#user-content-extra-parameters-other-than-constructors) section below)<br />

    If `class` is not provided Cascade will default to `Monolog\Formatter\LineFormatter`

- **_handlers_** - the derived associative array (from the Yaml or JSON) in which each key is the handler identifier holds keys/values to configure your handlers.<br />The following keys are _reserved_:
    - `class` (optional): classname of the handler you would like to use
    - `formatter` (optional): formatter identifier that you have defined
    - `processors` (optional): array of processor identifiers that you have defined
    - `handlers` (optional): array of handler identifiers that you have defined
    - `handler` (optional): single handler identifier that you have defined

    Other parameters will be interpreted as constructor parameters for that Handler class and passed in when the handler object is instantiated by the Cascade config loader.<br />
    If some parameters are not present in the constructor, they will be interpreted as extra parameters and Cascade will try to interpret them should they match any custom handler functions that are able to use them. (see [Extra Parameters](#user-content-extra-parameters-other-than-constructors) section below)

    If class is not provided Cascade will default to `Monolog\Handler\StreamHandler`

- **_processors_** - the derived associative array (from the Yaml or JSON) in which each key is the processor identifier holds keys/values to configure your processors.<br />The following key is _reserved_:
    - `class` (required): classname of the processor you would like to use

- **_loggers_** - the derived array (from the Yaml or JSON) in which each key is the logger identifier may contain only a `handlers` key and/or a `processors` key. You can decide what handler(s) and/or processor(s) you would like your logger to use.

**Note**: If you would like to use objects as parameters for your handlers, you can pass a class name (using the `class` option) with the corresponding arguments just like you would configure your handler. Cascade recursively instantiates and loads those objects as it parses the config file. See [this sample config file](https://github.com/theorchard/monolog-cascade/blob/master/examples/dependency_config.yml).

#### Parameter case
You can use either _underscored_ or _camelCased_ style in your config files, it does not matter. However, it is important that they match the names of the arguments from the constructor method.

```php
public function __construct($level = Logger::ERROR, $bubble = true, $appName = null)
```

Using a Yaml file:
```yaml
    level: ERROR,
    bubble: true,
    app_name: "some app that I wrote"
```

Cascade will _camelCase_ all the names of your parameters internally prior to be passed to the constructors.

#### Optional keys
`formatters`, `handlers` and `processors` keys are optional. If ommitted Cascade will default to Monolog's default formatter and handler: `Monolog\Formatter\LineFormatter` and `Monolog\Handler\StreamHandler` to `stderr`. If `processors` is ommitted, your logger(s) won't use any.

#### Default parameters
If a constructor method provides default value(s) in their declaration, Cascade will look it up and identify those parameters as optional with their default values. It can therefore be ommitted in your config file.

#### Order of sections and params
Order of the sections within the config file has no impact as long as they are formatted properly.
<br />Order of parameters does not matter either.

#### Extra parameters (other than constructor's)
You may want to have your Formatters and/or Handlers consume values other than via the constructor. Some methods may be called to do additional set up when configuring your loggers. Cascade interprets those extra params 3 different ways and will try do so in that order:

1. _Instance method_
    <br />Your Formatter or Handler has a defined method that takes a param as input. In that case you can write it as follow in your config file:

    ```yaml
    formatters:
      spaced:
          class: Monolog\Formatter\LineFormatter
          format: "%datetime% %channel%.%level_name%  %message%\n"
          include_stacktraces: true
    ```
    In this example, the `LineFormatter` class has an `includeStacktraces` method that takes a boolean. This method will be called upon instantiation.<br />

2. _Public member_
    <br />Your Formatter or Handler has a public member that can be set.

    ```yaml
    formatters:
        spaced:
            class: Monolog\Formatter\SomeFormatter
            some_public_member: "some value"
    ```
    In this example, the public member will be set to the passed in value upon instantiation.<br />

3. _Custom handler function_
    <br />See `FormatterLoader::initExtraOptionsHandlers` and `HandlerLoader::initExtraOptionsHandlers`. Those methods hold closures that can call instance methods if needed. The closure takes the instance and the parameter value as input.

    ```php
    self::$extraOptionHandlers = array(
        'Monolog\Formatter\LineFormatter' => array(
            'includeStacktraces' => function ($instance, $include) {
                $instance->includeStacktraces($include);
            }
        )
    );
    ```
    You can add handlers at runtime if needed. (i.e. if you write your logger handler for instance)

Running Tests
-------------

Just run Phpunit:
```sh
$ phpunit tests/
```

Contributing
------------

This extension is open source. Feel free to contribute and send a pull request!

Make sure your code follows the [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) standards, is documented and has unit tests.


What's next?
------------
 - add support for `.ini` config files
 - add support for namespaced Loggers with message propagation (through handler inheritance) so children loggers log messages using parent's handlers
 - add more custom function handlers to cover all the possible options of the current Monolog Formatters and Handlers
 - ~~add support for Processors (DONE)~~
 - ~~add support for DB/Store and other handlers requiring injection into the constructor ([issue #30](https://github.com/theorchard/monolog-cascade/issues/30)) (DONE)~~
 - other suggestions?


Symfony Users
-------------
You may want to use [MonologBundle](https://github.com/symfony/MonologBundle) as it integrates directly with your favorite framework.


Under The Hood
--------------
Here is a [Medium post](https://medium.com/orchard-technology/enhancing-monolog-699efff1051d#.dw6qu1c2p) if you want to know more about the implementation.
