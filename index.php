<?php

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Set timezone
date_default_timezone_set('Europe/Istanbul');

// Include configuration
require_once __DIR__ . '/app/Config/config.php';

// Check database connection
if (!defined('DB_CONNECTION')) {
    die('Database configuration not found');
}

// Include helpers
require_once __DIR__ . '/app/Helpers/functions.php';

// Include routes
require_once __DIR__ . '/app/Config/routes.php';

// Get URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('?', $uri)[0];

// Remove trailing slash if present
$uri = rtrim($uri, '/');

// Route the request
if (isset($routes[$uri])) {
    $controller = $routes[$uri]['controller'];
    $action = $routes[$uri]['action'];
    
    // Include and instantiate controller
    require_once __DIR__ . '/app/Controllers/' . $controller . '.php';
    $controllerInstance = new $controller();
    
    // Call action method
    $controllerInstance->$action();
} else {
    // Handle 404
    http_response_code(404);
    require_once __DIR__ . '/app/Views/errors/404.php';
}
