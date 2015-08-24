<?php
require_once(realpath(__DIR__.'/../vendor/autoload.php'));

use Cascade\Cascade;

// For these to work you will need php-redis and raven/raven

// You will want to update this file with a valid dsn
$loggerConfigFile = realpath(__DIR__.'/dependency_config.yml');

Cascade::fileConfig($loggerConfigFile);
Cascade::getLogger('dependency')->info('Well, that works!');
Cascade::getLogger('dependency')->error('Maybe not...');
