<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
if (file_exists($loader = __DIR__.'/../vendor/autoload.php')) {
    require $loader;
}

// Start the response...
($app = require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
