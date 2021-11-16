<?php

declare(strict_types=1);

use App\Controllers\DbController;
use App\Authentication\AuthenticationController;

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
            'handler' => [AuthenticationController::class, 'login']
        ],
        // Wildcards need to be at the bottom.
    ]
];
