<?php
require_once(realpath(__DIR__.'/../vendor/autoload.php'));

use Cascade\Cascade;

$logger = Cascade::getLogger('some_logger');
$logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout'));
$logger->info('Hellooooo World!');

// you should see the following in the stdout:
//    [YYYY-mm-dd hh:mm:ss] some_logger.INFO: Hellooooo World!
