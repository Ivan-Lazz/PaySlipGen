<?php

use Core\Router;

/**
 * Web Routes
 * 
 * These routes are for any web-based views if needed
 * Currently, the system is API-only, but this file is here
 * for future expansion if web views are required
 */

$router = app('router');

// Example: Home page (if needed)
// $router->get('/', 'HomeController@index');

// Example: Documentation page
// $router->get('/docs', 'DocumentationController@index');

// Fallback route - redirect to API docs
$router->get('/', function() {
    header('Location: /docs/api.md');
    exit;
});