<?php

/**
 * Get environment variable
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function env($key, $default = null)
{
    $value = getenv($key);
    
    if ($value === false) {
        return $default;
    }
    
    // Convert boolean-like values
    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return null;
    }
    
    // Handle quoted values
    if (strlen($value) > 1 && $value[0] === '"' && $value[-1] === '"') {
        return substr($value, 1, -1);
    }
    
    return $value;
}

/**
 * Get application container or resolve an entry
 *
 * @param string|null $key
 * @return mixed
 */
function app($key = null)
{
    if ($key === null) {
        return Core\App::container();
    }
    
    return Core\App::resolve($key);
}

/**
 * Get configuration value
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function config($key, $default = null)
{
    // For testing environments, return some default values
    if (defined('TESTING') && TESTING) {
        $testConfig = [
            'app.key' => 'base64:testing-key',
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'logging.channel' => 'testing',
            'logging.channels.testing.driver' => 'null'
        ];
        
        if (isset($testConfig[$key])) {
            return $testConfig[$key];
        }
    }
    
    $parts = explode('.', $key);
    $filename = array_shift($parts);
    
    static $configs = [];
    
    if (!isset($configs[$filename])) {
        $path = dirname(__DIR__) . "/config/{$filename}.php";
        
        if (file_exists($path)) {
            $configs[$filename] = require $path;
        } else {
            $configs[$filename] = [];
        }
    }
    
    $config = $configs[$filename];
    
    foreach ($parts as $part) {
        if (!isset($config[$part])) {
            return $default;
        }
        
        $config = $config[$part];
    }
    
    return $config;
}

/**
 * Get logger instance or log a message
 *
 * @param string|null $message
 * @param array $context
 * @param string $level
 * @return mixed
 */
function logger($message = null, array $context = [], $level = 'info')
{
    // For testing, return a null logger
    if (defined('TESTING') && TESTING) {
        static $testLogger = null;
        
        if ($testLogger === null) {
            $testLogger = new class {
                public function __call($name, $arguments) {
                    // Null logger
                    return null;
                }
            };
        }
        
        if ($message === null) {
            return $testLogger;
        }
        
        return $testLogger->$level($message, $context);
    }
    
    $logger = app('logger');
    
    if ($message === null) {
        return $logger;
    }
    
    return $logger->$level($message, $context);
}