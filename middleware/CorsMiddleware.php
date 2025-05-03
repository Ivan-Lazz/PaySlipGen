<?php

namespace App\Middleware;

use Core\Request;

class CorsMiddleware
{
    /**
     * Handle the incoming request
     *
     * @param \Core\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        // Get response from next middleware or controller
        $response = $next($request);
        
        // Add CORS headers
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        
        return $response;
    }
}