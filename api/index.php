<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Setup Vercel temporary directories
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/storage/logs',
    '/tmp/storage/app',
    '/tmp/storage/bootstrap/cache'
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Override environment variables for Vercel's read-only filesystem
$_ENV['APP_ENV'] = 'local';
putenv('APP_ENV=local');

$_ENV['APP_DEBUG'] = 'true';
putenv('APP_DEBUG=true');

$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

$_ENV['SESSION_DRIVER'] = 'cookie';
putenv('SESSION_DRIVER=cookie');

$_ENV['LOG_CHANNEL'] = 'stderr';
putenv('LOG_CHANNEL=stderr');

$_ENV['CACHE_STORE'] = 'array';
putenv('CACHE_STORE=array');

$_ENV['APP_CONFIG_CACHE'] = '/tmp/storage/bootstrap/cache/config.php';
putenv('APP_CONFIG_CACHE=/tmp/storage/bootstrap/cache/config.php');

$_ENV['APP_EVENTS_CACHE'] = '/tmp/storage/bootstrap/cache/events.php';
putenv('APP_EVENTS_CACHE=/tmp/storage/bootstrap/cache/events.php');

$_ENV['APP_PACKAGES_CACHE'] = '/tmp/storage/bootstrap/cache/packages.php';
putenv('APP_PACKAGES_CACHE=/tmp/storage/bootstrap/cache/packages.php');

$_ENV['APP_ROUTES_CACHE'] = '/tmp/storage/bootstrap/cache/routes.php';
putenv('APP_ROUTES_CACHE=/tmp/storage/bootstrap/cache/routes.php');

$_ENV['APP_SERVICES_CACHE'] = '/tmp/storage/bootstrap/cache/services.php';
putenv('APP_SERVICES_CACHE=/tmp/storage/bootstrap/cache/services.php');

// Maintenance mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoloader
if (file_exists($loader = __DIR__.'/../vendor/autoload.php')) {
    require $loader;
}

// Bootstrap app
$app = require_once __DIR__.'/../bootstrap/app.php';

// Override the main storage path to /tmp
$app->useStoragePath('/tmp/storage');

// Force HTTPS since Vercel SSL termination happens at the edge
$_SERVER['HTTPS'] = 'on';

// Handle the request
$app->handleRequest(Request::capture());
