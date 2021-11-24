<?php

declare(strict_types=1);

return [
    'servers' => [
        [
            'name' => 'The Box',
            'host' => '127.0.0.1',
            'excluded_tables' => ['mysql', 'sys', 'information_schema', 'performance_schema'],
        ],
    ]
];
