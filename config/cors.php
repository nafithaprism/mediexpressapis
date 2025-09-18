<?php
return [
    'paths' => ['v1/*', 'api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['GET','POST','PUT','PATCH','DELETE','OPTIONS'],
    'allowed_origins' => [
        'https://medi-express.prismcloudhosting.com',
        'https://cms-medi-express.prismcloudhosting.com',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],          // (or list explicitly)
    'exposed_headers' => ['ETag'],
    'max_age' => 86400,
    'supports_credentials' => false, 
];
