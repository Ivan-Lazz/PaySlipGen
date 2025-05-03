<?php

// Define the application path
define('APP_PATH', dirname(__DIR__));

// Autoload dependencies
require APP_PATH . '/vendor/autoload.php';

// Load helper functions
require APP_PATH . '/helpers/app_helpers.php';

// Initialize application
Core\App::init();

// Process request
$request = new Core\Request();

// Load routes
require APP_PATH . '/routes/api.php';

// Dispatch request
$response = app('router')->dispatch($request);

// Send response
echo $response;