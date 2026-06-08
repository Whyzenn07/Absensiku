<?php
/**
 * AbsensiKu — Setup Script
 * Jalankan SEKALI lalu HAPUS file ini!
 * Akses: https://domain-kamu.infinityfreeapp.com/setup.php
 */

// Kunci keamanan — ganti dengan string random sebelum upload
define('SETUP_KEY', 'absensiku2024setup');

if (!isset($_GET['key']) || $_GET['key'] !== SETUP_KEY) {
    die('<h2 style="color:red;font-family:sans-serif">Akses ditolak. Tambahkan ?key=absensiku2024setup di URL</h2>');
}

define('LARAVEL_START', microtime(true));

require __DIR__.'/laravel/vendor/autoload.php';

$app = require_once __DIR__.'/laravel/bootstrap/app.php';

$app->bind('path.public', function () {
    return __DIR__;
});

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo '<!DOCTYPE html><html lang="id"><head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>AbsensiKu Setup</title>
<style>
  body{font-family:system-ui,sans-serif;background:#f1f5f9;padding:2rem;max-width:600px;margin:0 auto}
  h1{color:#1e293b}
  .ok{background:#dcfce7;border-left:4px solid #16a34a;padding:1rem;margin:.5rem 0;border-radius:.5rem}
  .err{background:#fee2e2;border-left:4px solid #dc2626;padding:1rem;margin:.5rem 0;border-radius:.5rem}
  pre{white-space:pre-wrap;font-size:.8rem;margin:.5rem 0 0}
  .done{background:#2563eb;color:#fff;padding:1rem;border-radius:.75rem;text-align:center;margin-top:1.5rem}
</style></head><body>
<h1>AbsensiKu — Setup Database</h1>';

// Migrate
try {
    Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $out = Illuminate\Support\Facades\Artisan::output();
    echo '<div class="ok"><strong>✅ Migrate berhasil</strong><pre>'.htmlspecialchars($out).'</pre></div>';
} catch (Throwable $e) {
    echo '<div class="err"><strong>❌ Migrate gagal:</strong><pre>'.htmlspecialchars($e->getMessage()).'</pre></div>';
}

// Seed
try {
    Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    $out = Illuminate\Support\Facades\Artisan::output();
    echo '<div class="ok"><strong>✅ Seed berhasil</strong><pre>'.htmlspecialchars($out).'</pre></div>';
} catch (Throwable $e) {
    echo '<div class="err"><strong>❌ Seed gagal:</strong><pre>'.htmlspecialchars($e->getMessage()).'</pre></div>';
}

// Cache config
try {
    Illuminate\Support\Facades\Artisan::call('config:cache');
    echo '<div class="ok"><strong>✅ Config cached</strong></div>';
} catch (Throwable $e) {
    echo '<div class="err">Config cache: '.htmlspecialchars($e->getMessage()).'</div>';
}

echo '<div class="done">
  <h2>🎉 Setup selesai!</h2>
  <p>⚠️ <strong>HAPUS file setup.php ini sekarang via File Manager!</strong></p>
  <p>Lalu buka: <a href="/" style="color:#fff">https://domain-kamu.infinityfreeapp.com</a></p>
</div>
</body></html>';
