<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Tangkap fatal error yang sebenarnya via shutdown function
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Jangan kirim output lagi kalau sudah ada output
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/plain');
        }
        echo "\n\n=== PHP FATAL ERROR ===\n";
        echo "Type: " . $error['type'] . "\n";
        echo "Message: " . $error['message'] . "\n";
        echo "File: " . $error['file'] . "\n";
        echo "Line: " . $error['line'] . "\n";
    }
});

$tmpCache = '/tmp/laravel/bootstrap/cache';
$tmpStorage = '/tmp/laravel/storage';

foreach ([
    $tmpCache,
    $tmpStorage . '/app/public',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/framework/testing',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/logs',
] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

$_ENV['APP_SERVICES_CACHE'] = $tmpCache . '/services.php';
$_ENV['APP_PACKAGES_CACHE'] = $tmpCache . '/packages.php';
$_ENV['APP_CONFIG_CACHE']   = $tmpCache . '/config.php';
$_ENV['APP_ROUTES_CACHE']   = $tmpCache . '/routes-v7.php';
$_ENV['APP_EVENTS_CACHE']   = $tmpCache . '/events.php';

// Clear stale config cache on every cold start so env var changes take effect
foreach ([$_ENV['APP_CONFIG_CACHE'], $_ENV['APP_ROUTES_CACHE'], $_ENV['APP_SERVICES_CACHE'], $_ENV['APP_PACKAGES_CACHE'], $_ENV['APP_EVENTS_CACHE']] as $cacheFile) {
    if (file_exists($cacheFile)) {
        @unlink($cacheFile);
    }
}

if (file_exists($maintenance = $tmpStorage . '/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath($tmpStorage);

$app->handleRequest(Request::capture());
