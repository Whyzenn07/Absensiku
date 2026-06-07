<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Serve static files from public/ directly (before booting Laravel).
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$publicDir   = __DIR__ . '/../public';
$resolvedPublic = realpath($publicDir);
$staticFile  = ($resolvedPublic !== false) ? realpath($publicDir . $requestPath) : false;

if (
    $requestPath !== '/' &&
    $staticFile !== false &&
    $resolvedPublic !== false &&
    strncmp($staticFile, $resolvedPublic, strlen($resolvedPublic)) === 0 &&
    is_file($staticFile)
) {
    $ext = strtolower(pathinfo($staticFile, PATHINFO_EXTENSION));
    $mime = [
        'css'         => 'text/css',
        'js'          => 'application/javascript',
        'json'        => 'application/json',
        'map'         => 'application/json',
        'png'         => 'image/png',
        'jpg'         => 'image/jpeg',
        'jpeg'        => 'image/jpeg',
        'gif'         => 'image/gif',
        'svg'         => 'image/svg+xml',
        'ico'         => 'image/x-icon',
        'woff'        => 'font/woff',
        'woff2'       => 'font/woff2',
        'ttf'         => 'font/ttf',
        'eot'         => 'application/vnd.ms-fontobject',
        'webp'        => 'image/webp',
        'txt'         => 'text/plain',
        'xml'         => 'application/xml',
        'webmanifest' => 'application/manifest+json',
    ][$ext] ?? 'application/octet-stream';

    header('Content-Type: ' . $mime);
    header('Cache-Control: public, max-age=31536000, immutable');
    readfile($staticFile);
    exit;
}

// Maintenance mode check.
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Bootstrap Laravel (11.x pattern).
require __DIR__ . '/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->bind('path.public', function () {
    return __DIR__ . '/../public';
});

$app->handleRequest(Request::capture());
