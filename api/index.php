<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Setup Vercel temporary directories
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/storage/logs',
    '/tmp/storage/app'
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Override environment variables for Vercel's read-only filesystem
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

$_ENV['SESSION_DRIVER'] = 'cookie';
putenv('SESSION_DRIVER=cookie');

$_ENV['LOG_CHANNEL'] = 'stderr';
putenv('LOG_CHANNEL=stderr');

$_ENV['CACHE_STORE'] = 'array';
putenv('CACHE_STORE=array');

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

// Handle the request
$app->handleRequest(Request::capture());
