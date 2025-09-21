<?php

return [
    // Use wildcards so CORS applies to nested endpoints
    'paths' => ['v1/*', 'api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://medi-express.prismcloudhosting.com',
        'https://cms-medi-express.prismcloudhosting.com',
    ],

    'allowed_origins_patterns' => [],

    // '*' is fine if you’re not using credentials
    'allowed_headers' => ['*'],

    'exposed_headers' => [],
    'max_age' => 86400,
    'supports_credentials' => false,
];
