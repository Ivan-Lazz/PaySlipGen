<?php

namespace App\Middleware;

use Core\Request;
use Core\Response;

class RateLimitMiddleware
{
    /**
     * The maximum number of requests per minute
     *
     * @var int
     */
    protected $maxRequests = 60;
    
    /**
     * The time window in seconds
     *
     * @var int
     */
    protected $timeWindow = 60;
    
    /**
     * Create a new middleware instance
     *
     * @param int $maxRequests
     * @param int $timeWindow
     */
    public function __construct($maxRequests = null, $timeWindow = null)
    {
        if ($maxRequests !== null) {
            $this->maxRequests = $maxRequests;
        }
        
        if ($timeWindow !== null) {
            $this->timeWindow = $timeWindow;
        }
    }
    
    /**
     * Handle the incoming request
     *
     * @param \Core\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        // Get client IP
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        
        // Generate key for this client
        $key = "rate_limit:{$ip}";
        
        // In a real implementation, this would use Redis or a similar service
        // For this example, we'll simulate it with a temporary file
        $file = dirname(__DIR__) . "/storage/rate_limits/{$ip}.json";
        $dir = dirname($file);
        
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
        } else {
            $data = [
                'requests' => [],
                'last_reset' => time()
            ];
        }
        
        // Remove requests outside the time window
        $data['requests'] = array_filter($data['requests'], function($timestamp) {
            return $timestamp > time() - $this->timeWindow;
        });
        
        // Add current request
        $data['requests'][] = time();
        
        // Save data
        file_put_contents($file, json_encode($data));
        
        // Check if rate limit is exceeded
        if (count($data['requests']) > $this->maxRequests) {
            return Response::error('Too many requests', 429, [
                'retry_after' => $this->timeWindow
            ]);
        }
        
        // Set rate limit headers
        $response = $next($request);
        $response->setHeader('X-RateLimit-Limit', $this->maxRequests);
        $response->setHeader('X-RateLimit-Remaining', $this->maxRequests - count($data['requests']));
        $response->setHeader('X-RateLimit-Reset', time() + $this->timeWindow);
        
        return $response;
    }
}