<?php

namespace Core;

/**
 * Application class
 */
class App
{
    /**
     * The application container
     * 
     * @var Container
     */
    private static $container;
    
    /**
     * Initialize the application
     * 
     * @return void
     */
    public static function init()
    {
        // Load environment variables
        self::loadEnvironment();
        
        // Initialize container if not set
        if (!self::$container) {
            self::$container = new Container();
        }
        
        // Register core services
        self::registerServices();
    }
    
    /**
     * Load environment variables
     * 
     * @return void
     */
    private static function loadEnvironment()
    {
        $envFile = dirname(__DIR__) . '/.env';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                }
            }
        }
    }
    
    /**
     * Register core services in the container
     * 
     * @return void
     */
    private static function registerServices()
    {
        // Register database connection
        self::$container->singleton('db', function() {
            return Database::getInstance()->getConnection();
        });
        
        // Register logger
        self::$container->singleton('logger', function() {
            return new Logger(config('logging.channel'));
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
    
    /**
     * Get the application container
     * 
     * @return Container
     */
    public static function container()
    {
        return self::$container;
    }
    
    /**
     * Set the application container (for testing)
     * 
     * @param Container $container
     * @return void
     */
    public static function setContainer(Container $container)
    {
        self::$container = $container;
    }
    
    /**
     * Resolve a dependency from the container
     * 
     * @param string $key
     * @return mixed
     */
    public static function resolve($key)
    {
        return self::$container->make($key);
    }
}