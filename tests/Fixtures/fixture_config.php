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
        ),

        'group_handler' => array(
            'class' => 'Monolog\Handler\GroupHandler',
            'handlers' => array(
                'console',
                'info_file_handler',
            ),
        ),

        'fingers_crossed_handler' => array(
            'class' => 'Monolog\Handler\FingersCrossedHandler',
            'handler' => 'group_handler',
        ),
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
