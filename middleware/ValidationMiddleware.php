<?php

namespace App\Middleware;

use Core\Request;
use Core\Response;
use Core\Validator;

class ValidationMiddleware
{
    protected $rules;
    
    public function __construct(array $rules)
    {
        $this->rules = $rules;
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
        $validator = new Validator($request->all(), $this->rules);
        
        if (!$validator->passes()) {
            return Response::error('Validation failed', 400, $validator->errors());
        }
        
        return $next($request);
    }
}