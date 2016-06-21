<?php
return [
    'dux.use_cache' => [
        'files' => [
            'type' => 'files',
            'path' => DATA_PATH . 'cache/',
            'group' => 'tmp',
            'deep' => 0,
        ],
        'redis' => [
            'type' => 'redis',
            'host' => '127.0.0.1',
            'port' => 6379,
            'group' => 0,
        ],
    ]
];