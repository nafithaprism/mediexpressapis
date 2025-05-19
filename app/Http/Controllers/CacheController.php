<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class CacheController extends Controller
{
    /**
     * Clear the application cache.
     *
     * @return \Illuminate\Http\Response
     */
    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        return response()->json(['message' => 'Cache cleared successfully'], 200);
    }
}
