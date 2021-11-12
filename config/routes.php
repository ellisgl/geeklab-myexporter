<?php

declare(strict_types=1);

use App\Controllers\DbController;
use App\Controllers\LoginController;

return [
    'routes' => [
        [
            'methods' => ['GET'],
            'path'    => '/db',
            'handler' => [DbController::class, 'index']
        ],
        [
            'methods' => ['GET', 'POST'],
            'path'    => '/',
            'handler' => [LoginController::class, 'index']
        ],
        // Wildcards need to be at the bottom.
    ]
];
