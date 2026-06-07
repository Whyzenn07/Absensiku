<?php
define('LARAVEL_START', microtime(true));

// Serve static files from public/ directly (nginx-like behaviour).
// This runs before Laravel boots so asset requests are fast.
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$publicDir   = __DIR__ . '/../public';
$staticFile  = realpath($publicDir . $requestPath);

if (
    $requestPath !== '/' &&
    $staticFile !== false &&
    strncmp($staticFile, realpath($publicDir), strlen(realpath($publicDir))) === 0 &&
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

// Dynamic request — bootstrap Laravel and handle via the HTTP kernel.
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->bind('path.public', function () {
    return __DIR__ . '/../public';
});

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();
$kernel->terminate($request, $response);
