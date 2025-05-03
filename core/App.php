<?php

namespace Core;

class App
{
    private static $container;
    
    public static function init()
    {
        // Load environment variables
        self::loadEnvironment();
        
        // Initialize container
        self::$container = new Container();
        
        // Register core services
        self::registerServices();
    }
    
    private static function loadEnvironment()
    {
        $envFile = dirname(__DIR__) . '/.env';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                // Skip comments
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    // Remove quotes if present
                    if (preg_match('/^"(.*)"$/', $value, $matches)) {
                        $value = $matches[1];
                    } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
                        $value = $matches[1];
                    }
                    
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                }
            }
        }
    }
    
    private static function registerServices()
    {
        // Register database connection
        self::$container->singleton('db', function() {
            return Database::getInstance();
        });
        
        // Register logger
        self::$container->singleton('logger', function() {
            return new Logger(config('logging.default'));
        });
        
        // Register router
        self::$container->singleton('router', function() {
            return new Router();
        });
        
        // Register request
        self::$container->singleton('request', function() {
            return new Request();
        });
        
        // Register response
        self::$container->singleton('response', function() {
            return new Response();
        });
    }
    
    public static function container()
    {
        return self::$container;
    }
    
    public static function resolve($key)
    {
        return self::$container->make($key);
    }
}   