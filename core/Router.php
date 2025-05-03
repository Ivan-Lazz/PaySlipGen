<?php

namespace Core;

class Router
{
    protected $routes = [];
    protected $currentGroupPrefix = '';
    protected $currentGroupMiddleware = [];
    
    public function get($uri, $action)
    {
        return $this->addRoute('GET', $uri, $action);
    }
    
    public function post($uri, $action)
    {
        return $this->addRoute('POST', $uri, $action);
    }
    
    public function put($uri, $action)
    {
        return $this->addRoute('PUT', $uri, $action);
    }
    
    public function delete($uri, $action)
    {
        return $this->addRoute('DELETE', $uri, $action);
    }
    
    public function group(array $attributes, $callback)
    {
        $previousGroupPrefix = $this->currentGroupPrefix;
        $previousGroupMiddleware = $this->currentGroupMiddleware;
        
        // Merge prefix
        $this->currentGroupPrefix = $previousGroupPrefix;
        if (isset($attributes['prefix'])) {
            $this->currentGroupPrefix .= '/' . trim($attributes['prefix'], '/');
        }
        
        // Merge middleware
        $this->currentGroupMiddleware = $previousGroupMiddleware;
        if (isset($attributes['middleware'])) {
            $middleware = is_array($attributes['middleware']) 
                ? $attributes['middleware'] 
                : [$attributes['middleware']];
                
            $this->currentGroupMiddleware = array_merge(
                $this->currentGroupMiddleware,
                $middleware
            );
        }
        
        // Execute the group callback
        $callback($this);
        
        // Restore previous group settings
        $this->currentGroupPrefix = $previousGroupPrefix;
        $this->currentGroupMiddleware = $previousGroupMiddleware;
    }
    
    protected function addRoute($method, $uri, $action)
    {
        // Normalize URI
        $uri = $this->currentGroupPrefix . '/' . trim($uri, '/');
        $uri = trim($uri, '/');
        
        // Parse controller and method if string
        if (is_string($action)) {
            list($controller, $method) = explode('@', $action);
            $action = ['controller' => $controller, 'method' => $method];
        }
        
        // Add middleware from group
        if (!empty($this->currentGroupMiddleware)) {
            $action['middleware'] = array_merge(
                $action['middleware'] ?? [],
                $this->currentGroupMiddleware
            );
        }
        
        // Store the route
        $this->routes[$method][$uri] = $action;
        
        return $this;
    }
    
    public function dispatch($request)
    {
        $method = $request->getMethod();
        $uri = $request->getPath();
        $uri = trim($uri, '/');
        
        // Look for exact match
        if (isset($this->routes[$method][$uri])) {
            return $this->runRoute($request, $this->routes[$method][$uri]);
        }
        
        // Look for dynamic routes
        foreach ($this->routes[$method] as $route => $action) {
            $pattern = $this->compileRoutePattern($route);
            
            if (preg_match($pattern, $uri, $matches)) {
                // Remove the full match
                array_shift($matches);
                
                // Add parameters to action
                $action['parameters'] = $matches;
                
                return $this->runRoute($request, $action);
            }
        }
        
        // No route found
        return Response::error('Not Found', 404);
    }
    
    protected function compileRoutePattern($route)
    {
        // Replace route parameters with regex patterns
        $route = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route);
        
        // Create the pattern
        return "#^{$route}$#";
    }
    
    protected function runRoute($request, $action)
    {
        // Run middleware if defined
        if (isset($action['middleware'])) {
            foreach ($action['middleware'] as $middleware) {
                $instance = $this->resolveMiddleware($middleware);
                $result = $instance->handle($request, function ($request) {
                    return null;
                });
                
                if ($result !== null) {
                    return $result;
                }
            }
        }
        
        // Resolve controller
        $controller = $this->resolveController($action['controller']);
        $method = $action['method'];
        $parameters = $action['parameters'] ?? [];
        
        // Run the action
        return call_user_func_array([$controller, $method], $parameters);
    }
    
    protected function resolveController($controller)
    {
        // Prepend namespace if not already fully qualified
        if (strpos($controller, '\\') === false) {
            $controller = "\\App\\Controllers\\{$controller}";
        }
        
        return new $controller();
    }
    
    protected function resolveMiddleware($middleware)
    {
        // Prepend namespace if not already fully qualified
        if (strpos($middleware, '\\') === false) {
            $middleware = "\\App\\Middleware\\{$middleware}";
        }
        
        return new $middleware();
    }
}