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
        $dotenv = new \Dotenv\Dotenv(dirname(__DIR__));
        $dotenv->load();
    }
    
    private static function registerServices()
    {
        // Register database connection
        self::$container->singleton('db', function() {
            return Database::getInstance();
        });
        
        // Register logger
        self::$container->singleton('logger', function() {
            return new Logger(config('logging.channel'));
        });
        
        // Register router
        self::$container->singleton('router', function() {
            return new Router();
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