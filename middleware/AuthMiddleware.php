<?php

namespace App\Middleware;

use Core\Request;
use Core\Response;
use App\Services\AuthService;

class AuthMiddleware
{
    protected $authService;
    
    public function __construct()
    {
        $this->authService = new AuthService();
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
        $token = $request->getHeader('Authorization');
        
        if (!$token) {
            return Response::error('Unauthorized', 401);
        }
        
        // Remove Bearer prefix if present
        $token = str_replace('Bearer ', '', $token);
        
        $payload = $this->authService->verifyToken($token);
        
        if (!$payload) {
            return Response::error('Invalid token', 401);
        }
        
        // Set user info in request
        $request->user = $payload;
        
        return $next($request);
    }
}