<?php
// config/cors.php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'auth/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:5173'], // Your Vue frontend

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // ✅ Enable credentials
];
