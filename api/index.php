<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ── Static file shortcut (before Laravel boots) ───────────────────────────
$requestPath    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$publicDir      = __DIR__ . '/../public';
$resolvedPublic = realpath($publicDir);
$staticFile     = ($resolvedPublic !== false) ? realpath($publicDir . $requestPath) : false;

if (
    $requestPath !== '/' &&
    $staticFile !== false &&
    $resolvedPublic !== false &&
    strncmp($staticFile, $resolvedPublic, strlen($resolvedPublic)) === 0 &&
    is_file($staticFile)
) {
    $ext  = strtolower(pathinfo($staticFile, PATHINFO_EXTENSION));
    $mime = [
        'css'  => 'text/css', 'js' => 'application/javascript',
        'json' => 'application/json', 'map' => 'application/json',
        'png'  => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif', 'svg' => 'image/svg+xml', 'ico' => 'image/x-icon',
        'woff' => 'font/woff', 'woff2' => 'font/woff2', 'ttf' => 'font/ttf',
        'webp' => 'image/webp', 'txt' => 'text/plain', 'xml' => 'application/xml',
        'webmanifest' => 'application/manifest+json',
    ][$ext] ?? 'application/octet-stream';

    header('Content-Type: ' . $mime);
    header('Cache-Control: public, max-age=31536000, immutable');
    readfile($staticFile);
    exit;
}

// ── Redirect Laravel storage + bootstrap cache to /tmp (read-only on Vercel) ──
$tmpStorage = '/tmp/laravel-storage';
foreach ([
    'framework/cache/data',
    'framework/sessions',
    'framework/views',
    'framework/testing',
    'logs',
    'app/public',
    'bootstrap',
] as $dir) {
    $fullPath = "$tmpStorage/$dir";
    if (!is_dir($fullPath)) {
        mkdir($fullPath, 0755, true);
    }
}

// Redirect ProviderRepository cache files away from read-only bootstrap/cache/
foreach ([
    'APP_SERVICES_CACHE' => "$tmpStorage/bootstrap/services.php",
    'APP_PACKAGES_CACHE' => "$tmpStorage/bootstrap/packages.php",
] as $envKey => $tmpPath) {
    putenv("$envKey=$tmpPath");
    $_ENV[$envKey]    = $tmpPath;
    $_SERVER[$envKey] = $tmpPath;
}

// ── Copy pre-seeded SQLite to /tmp if not already there ───────────────────────
$sqliteDest = '/tmp/database.sqlite';
$sqliteSrc  = __DIR__ . '/../database/database.sqlite';
if (!file_exists($sqliteDest) && file_exists($sqliteSrc)) {
    copy($sqliteSrc, $sqliteDest);
}

// ── Force HTTPS on Vercel (SSL terminated at edge, Lambda receives HTTP) ─────
if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
    $_SERVER['HTTPS']       = $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'on' : 'off';
    $_SERVER['SERVER_PORT'] = $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 443 : 80;
}

// ── Maintenance mode ──────────────────────────────────────────────────────
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// ── Bootstrap Laravel 11 ──────────────────────────────────────────────────
require __DIR__ . '/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath($tmpStorage);

$app->bind('path.public', function () {
    return __DIR__ . '/../public';
});

$app->handleRequest(Request::capture());
