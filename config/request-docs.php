<?php

return [
    'url' => '/request-docs',

    'middlewares' => [
        // \Rakutentech\LaravelRequestDocs\LaravelRequestDocsMiddleware::class,
    ],

    'hide_matching' => [
        '#^_debugbar#',
        '#^request-docs#',
        '#^sanctum#',
    ],

    'open_api' => [
        'title' => 'E-Commerce API Documentation',
        'description' => 'API Documentation for E-Commerce Application',
        'version' => '1.0.0',
        'servers' => [
            [
                'url' => 'http://localhost:8000',
                'description' => 'Local Development',
            ],
        ],
    ],

    'sort_by' => 'default',

    'only_default_groups' => false,

    'debug_mode' => env('APP_DEBUG', false),
];
