<?php

namespace App\Middleware;

use Core\Request;

class LogMiddleware
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
        // Log start time
        $startTime = microtime(true);
        
        // Get request information
        $method = $request->getMethod();
        $uri = $request->getPath();
        
        // Log request
        logger()->info("Request: {$method} {$uri}", [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $request->getHeader('User-Agent')
        ]);
        
        // Process the request
        $response = $next($request);
        
        // Calculate execution time
        $executionTime = microtime(true) - $startTime;
        
        // Log response
        logger()->info("Response: {$method} {$uri}", [
            'status' => http_response_code(),
            'execution_time' => round($executionTime * 1000, 2) . 'ms'
        ]);
        
        return $response;
    }
}