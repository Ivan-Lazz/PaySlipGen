<?php

namespace Core;

use Closure;
use Exception;

/**
 * Dependency Injection Container
 */
class Container
{
    /**
     * Service bindings
     * 
     * @var array
     */
    protected $bindings = [];
    
    /**
     * Singleton instances
     * 
     * @var array
     */
    protected $instances = [];
    
    /**
     * Bind a service to the container
     * 
     * @param string $abstract
     * @param mixed $concrete
     * @return void
     */
    public function bind($abstract, $concrete)
    {
        $this->bindings[$abstract] = $concrete;
    }
    
    /**
     * Bind a singleton service to the container
     * 
     * @param string $abstract
     * @param mixed $concrete
     * @return void
     */
    public function singleton($abstract, $concrete)
    {
        $this->bind($abstract, $concrete);
        $this->instances[$abstract] = null;
    }
    
    /**
     * Resolve a service from the container
     * 
     * @param string $abstract
     * @return mixed
     */
    public function make($abstract)
    {
        // Return instance if it exists
        if (isset($this->instances[$abstract]) && $this->instances[$abstract] !== null) {
            return $this->instances[$abstract];
        }
        
        // Get the concrete implementation
        $concrete = $this->bindings[$abstract] ?? $abstract;
        
        // Create the instance
        $instance = $this->build($concrete);
        
        // If this is a singleton, store the instance
        if (array_key_exists($abstract, $this->instances)) {
            $this->instances[$abstract] = $instance;
        }
        
        return $instance;
    }
    
    /**
     * Build a concrete implementation
     * 
     * @param mixed $concrete
     * @return mixed
     */
    protected function build($concrete)
    {
        // If concrete is a Closure, execute it
        if ($concrete instanceof Closure) {
            return $concrete($this);
        }
        
        // If concrete is a class name, instantiate it
        if (is_string($concrete) && class_exists($concrete)) {
            return new $concrete();
        }
        
        // Otherwise, return concrete as is
        return $concrete;
    }
    
    /**
     * Check if a service is bound to the container
     * 
     * @param string $abstract
     * @return bool
     */
    public function has($abstract)
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }
    
    /**
     * Get all bindings
     * 
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }
    
    /**
     * Get all instances
     * 
     * @return array
     */
    public function getInstances()
    {
        return $this->instances;
    }
}