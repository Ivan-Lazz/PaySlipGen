<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that is used when writing
    | messages to the logs. The default value is "file" which uses a
    | simple file-based logging driver.
    |
    */
    'default' => getenv()('LOG_CHANNEL', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. The
    | available channels include: "file", "daily", and "syslog".
    |
    */
    'channels' => [
        'file' => [
            'driver' => 'file',
            'path' => dirname(__DIR__) . '/logs/app.log',
            'level' => getenv()('LOG_LEVEL', 'debug'),
        ],
        
        'daily' => [
            'driver' => 'daily',
            'path' => dirname(__DIR__) . '/logs/app.log',
            'level' => getenv()('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],
        
        'syslog' => [
            'driver' => 'syslog',
            'level' => getenv()('LOG_LEVEL', 'debug'),
        ],
        
        'null' => [
            'driver' => 'null',
        ],
    ],
];