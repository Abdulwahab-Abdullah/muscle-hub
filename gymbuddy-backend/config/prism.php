<?php

return [
    'prism_server' => [
        'middleware' => [],
        'enabled' => env('PRISM_SERVER_ENABLED', false), // false لأننا نرسل الأسئلة مباشرة للـ API
    ],
    'providers' => [
        'openrouter' => [
            'api_key' => env('GITHUB_API_KEY', ''), // استخدم المفتاح الموجود في .env
            'url' => env('OPENROUTER_URL', 'https://openrouter.ai/api/v1'),
            'site' => [
                'http_referer' => env('OPENROUTER_SITE_HTTP_REFERER', null),
                'x_title' => env('OPENROUTER_SITE_X_TITLE', null),
            ],
        ],
    ],
];
