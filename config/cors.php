<?php
return [
    'paths' => ['v1/*', 'api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['GET','POST','PUT','PATCH','DELETE','OPTIONS'],
    'allowed_origins' => [
        'https://medi-express.prismcloudhosting.com',
        'https://cms-medi-express.prismcloudhosting.com',
    ],
    'allowed_origins_patterns' => [],           // remove the wildcard pattern
    'allowed_headers' => ['*'],                 // or list explicitly if you prefer
    'exposed_headers' => ['ETag'],
    'max_age' => 86400,
    'supports_credentials' => false,            // set true ONLY if you actually use cookies
];
