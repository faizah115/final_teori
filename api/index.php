<?php
/**
 * Laravel entry point used by Vercel.
 *
 * This file mirrors the default `public/index.php` shipped with Laravel so that the
 * framework can resolve routes, middleware, and controllers when Vercel proxies all
 * requests to it (see `vercel.json`).
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

try {
/* --------------------------------------------------------------------------
 | Check For Maintenance Mode
 */
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/* --------------------------------------------------------------------------
 | Register The Auto Loader (provided by Composer)
 */
require __DIR__.'/../vendor/autoload.php';

// Fix for Vercel Read-Only Filesystem
$tmpPaths = [
    'APP_SERVICES_CACHE' => '/tmp/services.php',
    'APP_PACKAGES_CACHE' => '/tmp/packages.php',
    'APP_CONFIG_CACHE' => '/tmp/config.php',
    'APP_ROUTES_CACHE' => '/tmp/routes.php',
    'APP_EVENTS_CACHE' => '/tmp/events.php',
];
foreach ($tmpPaths as $key => $path) {
    $_ENV[$key] = $path;
    $_SERVER[$key] = $path;
    putenv("{$key}={$path}");
}

/* --------------------------------------------------------------------------
 | Run The Application
 */
$app = require __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Request::capture();

$response = $kernel->handle($request);

$response->send();

$kernel->terminate($request, $response);

// Force HTTPS (optional, can be removed if not needed)
$_SERVER['HTTPS'] = 'on';

} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Vercel Deployment Debug',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    exit;
}
