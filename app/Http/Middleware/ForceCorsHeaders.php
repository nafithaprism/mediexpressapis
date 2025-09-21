<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceCorsHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $origin = $request->headers->get('Origin');
        $allowed = [
            'https://medi-express.prismcloudhosting.com',
            'https://cms-medi-express.prismcloudhosting.com',
        ];

        if ($origin && in_array($origin, $allowed, true)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Vary', 'Origin');
        }

        return $response;
    }
}
